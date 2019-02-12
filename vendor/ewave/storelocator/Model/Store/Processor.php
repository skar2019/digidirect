<?php

namespace Ewave\StoreLocator\Model\Store;

use Ewave\StoreLocator\Component\AttributeSetNameNormalizer;
use Ewave\StoreLocator\Model\Config\Source\DisplayOnMap;
use Ewave\Locator\Model\ProcessorInterface;
use Ewave\StoreLocator\Helper\Config as ConfigHelper;
use Ewave\StoreLocator\Model\Config\Source\SortOrder;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Framework\Api\SortOrder as SortOrderApi;
use Ewave\AbstractEntity\Model\ResourceModel\AbstractEntityIndex;
use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\AbstractEntity\Model\AbstractEntity\Attribute\Source\VisibleOnFrontend;
use Ewave\AbstractEntity\Model\AbstractEntity\Attribute\Source\Status;
use Magento\Framework\App\Cache;
use \Magento\Framework\App\ResourceConnection;
use Ewave\AbstractEntity\Model\AbstractEntity\Media\Config as MediaConfig;
use Magento\Framework\UrlInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection;
use Ewave\StoreLocator\Helper\AeIndex as AeHelper;
use Magento\Catalog\Model\Template\Filter as CatalogTemplateFilter;
use Magento\Framework\DB\Select;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Processor implements ProcessorInterface
{
    const DISTANCE_FIELD = 'distance';
    const ADDITIONAL_SETTINGS = 'additional_settings';
    const COUNTRY_FILTER = 'country_filter';
    const SEARCH_TERM_PARAM = 'searchTerm';
    const SEARCH_PARAMS_DO_NOT_FILTER_BY_COUNTRY = 'do_not_filter_by_country';
    const EWAVE_STORE_LOCATOR_CACHE_TABLES = 'ewave_storelocator_cache_tables';

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var AbstractEntityIndex
     */
    protected $abstractEntityIndex;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var MediaConfig
     */
    protected $mediaConfig;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var CollectionFactory
     */
    protected $attributeSetCollectionFactory;

    /**
     * @var AeHelper
     */
    protected $aeHelper;

    /**
     * @var null
     */
    protected $setCollection = null;

    /**
     * @var AttributeSetNameNormalizer
     */
    protected $attributeSetNormalizer;

    /**
     * @var Cache
     */
    protected $cacheManager;

    /**
     * @var array
     */
    protected $existingTables = [];

    /**
     * @var CatalogTemplateFilter
     */
    protected $catalogTemplateFilter;

    /**
     * Processor constructor.
     * @param ConfigHelper $configHelper
     * @param AbstractEntityIndex $abstractEntityIndex
     * @param ResourceConnection $resourceConnection
     * @param MediaConfig $mediaConfig
     * @param UrlInterface $urlBuilder
     * @param CollectionFactory $collectionFactory
     * @param AeHelper $aeHelper
     * @param CatalogTemplateFilter $catalogTemplateFilter
     * @param AttributeSetNameNormalizer $attributeSetNameNormalizer
     * @param Cache $cache
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ConfigHelper $configHelper,
        AbstractEntityIndex $abstractEntityIndex,
        ResourceConnection $resourceConnection,
        MediaConfig $mediaConfig,
        UrlInterface $urlBuilder,
        CollectionFactory $collectionFactory,
        AeHelper $aeHelper,
        CatalogTemplateFilter $catalogTemplateFilter,
        AttributeSetNameNormalizer $attributeSetNameNormalizer,
        Cache $cache
    ) {
        $this->cacheManager = $cache;
        $this->attributeSetNormalizer = $attributeSetNameNormalizer;
        $this->configHelper = $configHelper;
        $this->abstractEntityIndex = $abstractEntityIndex;
        $this->resource = $resourceConnection;
        $this->mediaConfig = $mediaConfig;
        $this->urlBuilder = $urlBuilder;
        $this->attributeSetCollectionFactory = $collectionFactory;
        $this->aeHelper = $aeHelper;
        $this->catalogTemplateFilter = $catalogTemplateFilter;
    }

    /**
     * @param array $data
     * @param array $searchParams
     * @return array
     */
    public function process($data, $searchParams)
    {
        $result = [];
        $store = null;
        if (!empty($searchParams['store'])) {
            $store = $searchParams['store'];
        }
        $attributes = $this->configHelper->getAttributes();
        $setName = null;
        if (!empty($data['attribute_set'])) {
            $setName = $data['attribute_set'];
        }
        $callbacks = null;
        if (!empty($data['callbacks'])) {
            $callbacks = $data['callbacks'];
        }
        $items = $this->searchStores($setName, $searchParams, $attributes);
        $result['items'] = $this->prepareStores($items, $store, $callbacks);
        $result['settings'] = [
            'show_featured_stores_at_the_top' => $this->configHelper->isShowFeaturedStoresAtTheTop(),
            'sort_order' => $this->configHelper->getSortOrder(),
            'default_image' => $this->configHelper->getDefaultImage(),
            'exclusive_icon' => $this->configHelper->getExclusiveIcon(),
            'stores_on_locator_page' => $this->configHelper->getStoresOnLocatorPage(),
            'open_in_popup' => $this->configHelper->getOpenInPopup()
        ];

        try {
            /**
             * Usage: Add array ['setting_code' => 'xml_path'] to request params
             * It will automatically retrieve these settings from configuration
             */
            $additionalSettings = $searchParams[self::ADDITIONAL_SETTINGS] ?? [];
            if (!empty($additionalSettings) && is_array($additionalSettings)) {
                $settings = $this->configHelper->setRequiredSettings($additionalSettings)->getSettings();
                foreach ($settings as $settingCode => $settingValue) {
                    $result['settings'][$settingCode] = $settingValue;
                }
            }
        } catch (\Throwable $exception) {
            return $result;
        }

        return $result;
    }

    /**
     * @param string $storeLocatorName
     * @return mixed|string
     */
    protected function changeStoreLocatorName($storeLocatorName)
    {
        $storeLocatorName = $this->normalizeName($storeLocatorName);

        $history = $this->configHelper->getHistory();
        $currentId = $history[$storeLocatorName] ?? null;
        if (!$currentId) {
            //Means that was not changed
            return $storeLocatorName;
        }

        $attributeSet = $this->aeHelper->getAttributeSetById($currentId);
        if (!($attributeSetName = $attributeSet->getAttributeSetName())) {
            return $storeLocatorName;
        }

        if ($this->getExistsTableNameByAttributeSetName($attributeSetName)) {
            return $attributeSetName;
        }
        return $storeLocatorName;
    }

    /**
     * Get AE index table name by attribute set name and check if table exists
     *
     * @param string $attributeSetName
     * @return string|null
     */
    protected function getExistsTableNameByAttributeSetName($attributeSetName)
    {
        return $this->aeHelper->getExistsTableNameByAttributeSetName($attributeSetName);
    }

    /**
     * @param string $tableName
     * @return bool
     */
    protected function tableExists($tableName)
    {
        if (!isset($this->existingTables[$tableName])) {
            $cacheKey = static::EWAVE_STORE_LOCATOR_CACHE_TABLES . '_' . $tableName;
            $tableInCache = $this->cacheManager->load($cacheKey);
            try {
                $tables = $tableInCache;
            } catch (\Throwable $exception) {
                $tables = [];
            }
            if (!$tables) {
                $tableExists = !empty($this->abstractEntityIndex->getConnection()->getTables($tableName));
                if (false !== $tableExists) {
                    $this->cacheManager->save(
                        $tableExists,
                        $cacheKey,
                        [\Magento\Framework\View\Element\AbstractBlock::CACHE_GROUP]
                    );
                }
                $this->existingTables[$tableName] = $tableExists;
            } else {
                $this->existingTables[$tableName] = $tables;
            }
        }
        return !empty($this->existingTables[$tableName]);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function normalizeName(string $name)
    {
        return $this->attributeSetNormalizer->normalize($name);
    }

    /**
     * @param string $attributeSetName
     * @param array $searchParams
     * @param array $attributes
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function searchStores(
        $attributeSetName = ConfigHelper::ATTRIBUTE_SET_NAME,
        $searchParams = [],
        array $attributes = []
    ) {

        $attributeSetName = $this->changeStoreLocatorName($attributeSetName);

        $sortDistanceField = self::DISTANCE_FIELD;
        if (empty($searchParams['latitude'])
            || empty($searchParams['longitude'])
            || !empty($searchParams['specified_ids'])
            || !$this->aeHelper->isAttributeDescribed($attributeSetName, 'latitude')
            || !$this->aeHelper->isAttributeDescribed($attributeSetName, 'longitude')
        ) {
            $sortDistanceField = AbstractEntityInterface::NAME;
        }

        $searchTerm = isset($searchParams[self::SEARCH_TERM_PARAM]) ? $searchParams[self::SEARCH_TERM_PARAM] : null;
        if (empty(trim($searchTerm))) {
            $searchTerm = null;
        }

        $sortOrder = $this->getSortOrders($sortDistanceField);
        try {
            $attributes = $this->getExistsAttributes($attributeSetName, $attributes);
            $select = $this->abstractEntityIndex->searchEntities(
                $attributeSetName,
                $searchTerm,
                $attributes,
                $sortOrder,
                true,
                $searchParams['completeCoincidence'] ?? false
            );
        } catch (\Throwable $throwable) {
            $select = [];
        }

        if (is_array($select)) {
            return $select;
        }
        $select->where(
            AbstractEntityInterface::VISIBLE_ON_FRONTEND . '=?',
            VisibleOnFrontend::VISIBLE_ON_FRONTEND_ENABLED
        );
        $select->where(AbstractEntityInterface::STATUS . '=?', Status::STATUS_ENABLED);
        $select->where(ConfigHelper::ATTRIBUTE_DISPLAY_ON_MAP . '=?', DisplayOnMap::DISPLAY_ON_MAP_ENABLED);
        $this->addAttributeToFilter($select, $attributes, 'longitude', 'NOT ISNULL(longitude)');
        $this->addAttributeToFilter($select, $attributes, 'longitude', 'NOT ISNULL(latitude)');
        if (!empty($searchParams['specified_ids']) && is_array($searchParams['specified_ids'])) {
            $select->where('entity_id IN (?)', $searchParams['specified_ids']);
        } else {
            if (!empty($searchParams['latitude'])
                && $this->aeHelper->isAttributeDescribed($attributeSetName, 'latitude')
                && !empty($searchParams['longitude'])
                && $this->aeHelper->isAttributeDescribed($attributeSetName, 'longitude')
            ) {
                $defaultRadius = $this->configHelper->getDefaultRadius();
                $distance = empty($searchParams['radius']) ? $defaultRadius : $searchParams['radius'] * 1000;
                if (!empty($searchParams['without_radius']) && $searchParams['without_radius']) {
                    $distance = null;
                }
                $this->addLatLngToFilterDistance(
                    $select,
                    $searchParams['latitude'],
                    $searchParams['longitude'],
                    $distance
                );
            }

            /**
             * In case you need to display all stores in all countries
             */
            $doNotFilterByCountry = $searchParams[self::SEARCH_PARAMS_DO_NOT_FILTER_BY_COUNTRY] ?? null;
            if (!$doNotFilterByCountry) {
                $select->where('country  IN (?)', $this->getCountryFilter($searchParams));
            }
        }

        $limit = $this->configHelper->getMaxStoresToShow();
        if (!empty($searchParams['limit'])) {
            $limit = $searchParams['limit'];
        }
        $select->limit($limit);
        try {
            return $this->resource->getConnection()->fetchAll($select);
        } catch (\Throwable $exception) {
            return [];
        }
    }

    /**
     * @param Select $select
     * @param array $attributes
     * @param string $attribute
     * @param string $condition
     * @return void
     */
    protected function addAttributeToFilter(Select $select, array $attributes, $attribute, $condition)
    {
        if (in_array($attribute, $attributes)) {
            $select->where($condition);
        }
    }

    /**
     * @param string $attribteSetName
     * @param array $attributes
     * @return array
     */
    protected function getExistsAttributes($attribteSetName, array $attributes)
    {
        $tableName = $this->getExistsTableNameByAttributeSetName($attribteSetName);
        if ($tableName !== null) {
            $describedAttributes = $this->abstractEntityIndex->getConnection()->describeTable($tableName);
            $attributes = array_intersect($attributes, array_keys($describedAttributes));
        }
        return $attributes;
    }

    /**
     * @param array $searchParams
     * @return bool
     */
    protected function areCoordinatesPresented($searchParams)
    {
        return !empty($searchParams['latitude']) && !empty($searchParams['longitude']);
    }

    /**
     * @param array $searchParams
     * @return array
     */
    protected function getCountryFilter($searchParams)
    {
        $countryCodes = [];
        if ($this->isCountryFilterPresented($searchParams)) {
            $countryCodes = $searchParams[self::COUNTRY_FILTER] ?? [];
        }

        if (!is_array($countryCodes)) {
            $countryCodes = explode(',', $countryCodes);
        }

        $countryCodes = array_filter($countryCodes);

        if (!empty($countryCodes)) {
            return $countryCodes;
        }

        if ($this->areCoordinatesPresented($searchParams)) {
            return $this->configHelper->getAvailableCountries();
        }

        return [$this->configHelper->getDefaultCountry()];
    }

    /**
     * @param string $sortDistanceField
     * @return array
     */
    protected function getSortOrders($sortDistanceField = self::DISTANCE_FIELD)
    {
        $sortOrders = [];

        if ($this->configHelper->isShowFeaturedStoresAtTheTop()) {
            $sortOrders[] = ConfigHelper::ATTRIBUTE_FEATURED . ' ' . SortOrderApi::SORT_DESC;
        }
        // @codingStandardsIgnoreStart
        switch ($this->configHelper->getSortOrder()) {
            case SortOrder::SORT_PRIORITY:
                $priorityField = ConfigHelper::ATTRIBUTE_PRIORITY;
                $sortOrders[] = 'if(ISNULL(' . $priorityField . ') OR ' . $priorityField . '=0,0,1) '
                    . SortOrderApi::SORT_DESC;
                $sortOrders[] = ConfigHelper::ATTRIBUTE_PRIORITY . ' ' . SortOrderApi::SORT_ASC;
                $sortOrders[] = AbstractEntityInterface::ENTITY_ID . ' ' . SortOrderApi::SORT_ASC;
                break;
            case SortOrder::SORT_DISTANCE:
                $sortOrders[] = $sortDistanceField . ' ' . SortOrderApi::SORT_ASC;
                break;
            default:
                return $sortOrders;
        }
        // @codingStandardsIgnoreEnd

        return $sortOrders;
    }

    /**
     * @param array $items
     * @param null $store
     * @return array
     */
    protected function prepareStores($items, $store, $callbacks)
    {
        foreach ($items as &$item) {
            if (!empty($item['url_key'])) {
                if ($store) {
                    $this->urlBuilder->setScope($store);
                }
                $item['url_key'] = $this->urlBuilder->getUrl($item['url_key'], ['_nosid' => 1]);
            }

            if (!empty($item['image'])) {
                $item['image'] = $this->mediaConfig->getBaseMediaUrl() . $item['image'];
            }

            if (!empty($callbacks)) {
                foreach ($callbacks as $attribute => $callback) {
                    if (!empty($item[$attribute])) {
                        if (is_callable($callback)) {
                            $item[$attribute] = call_user_func_array($callback, [$item[$attribute]]);
                        }
                    }
                }
            }
        }

        return $items;
    }

    /**
     * @param \Magento\Framework\Db\Select $select
     * @param string $lat
     * @param string $lng
     * @param null|int $distance
     * @return $this
     */
    protected function addLatLngToFilterDistance($select, $lat, $lng, $distance = null)
    {
        // @codingStandardsIgnoreStart
        $expression = "(1609.34*((acos(sin(({{lat}}*pi()/180)) * sin((`{{latitude}}`*pi()/180))+cos(($lat *pi()/180)) * cos((`{{latitude}}`*pi()/180)) * cos((({{lng}} - `{{longitude}}`)*pi()/180))))*180/pi())*60*1.1515)";
        $this->addExpressionFieldToSelect($select, self::DISTANCE_FIELD, $expression,
            ['latitude' => 'latitude', 'longitude' => 'longitude', 'lat' => $lat, 'lng' => $lng]);
        // @codingStandardsIgnoreEnd
        if ($distance) {
            $select->having(self::DISTANCE_FIELD . ' <= ?', $distance);
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Db\Select $select
     * @param string $alias
     * @param string $expression
     * @param string $fields
     * @return $this
     */
    protected function addExpressionFieldToSelect($select, $alias, $expression, $fields)
    {
        // validate alias
        if (!is_array($fields)) {
            $fields = [$fields => $fields];
        }

        $fullExpression = $expression;
        foreach ($fields as $fieldKey => $fieldItem) {
            $fullExpression = str_replace('{{' . $fieldKey . '}}', $fieldItem, $fullExpression);
        }

        $select->columns([$alias => $fullExpression]);

        return $this;
    }

    /**
     * @param array $searchParams
     * @return bool
     */
    protected function isCountryFilterPresented($searchParams)
    {
        return isset($searchParams[self::COUNTRY_FILTER]);
    }
}
