<?php

namespace Ewave\Banner\Model;

use Ewave\Banner\Helper\IssetTrait;
use Ewave\Banner\Model\Attributes\AttributesInterface;
use Ewave\Banner\Model\Attributes\Config as AttributesConfig;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context as ModelContext;
use Magento\Framework\Registry;
use Magento\Framework\Data\Collection\AbstractDb;
use Ewave\Banner\Model\ResourceModel\Attributes as AttributesRM;
use Magento\Banner\Model\Banner as BannerModel;

/**
 *
 * @property \Ewave\Banner\Model\ResourceModel\Attributes $_resource
 */
class Attributes extends AbstractModel
{
    use IssetTrait;

    const CUSTOMER_SEGMENT_CACHE = 'banner_customer_segment_cache';

    /**
     * @var AttributesConfig
     */
    protected $attributesConfig;

    /**
     * @var array
     */
    protected $bannerAttributesByBannerId = [];

    /**
     * @var array
     */
    protected $bannerAttributesDuringLoadById = [];

    /**
     * @var array
     */
    protected $bannerCustomerSegment = null;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var CustomerSegment
     */
    protected $customerSegment;

    /**
     * Attributes constructor.
     *
     * @param ModelContext $context
     * @param Registry $registry
     * @param AttributesConfig $targetEntityConfig
     * @param AttributesRM $resource
     * @param Cache $cache
     * @param CustomerSegment $customerSegment
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        ModelContext $context,
        Registry $registry,
        AttributesConfig $targetEntityConfig,
        AttributesRM $resource,
        Cache $cache,
        CustomerSegment $customerSegment,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->customerSegment = $customerSegment;
        $this->attributesConfig = $targetEntityConfig;
        $this->cache = $cache;
    }

    /**
     * Set resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ewave\Banner\Model\ResourceModel\Attributes');
    }

    /**
     * @param BannerModel $banner
     * @return $this
     * @throws \Exception
     */
    public function setAttributes(BannerModel $banner)
    {
        $attributesToSave = [];
        $attributes = $this->getByKey($this->_data, 'banner_attributes', []);
        foreach ($attributes as $attributeCode => $attributeConfig) {
            $callback = $attributeConfig['custom'] ? '_setAttribute' : '_setValue';
            try {
                $attributesToSave[$attributeCode] = $this->$callback($banner, $attributeCode);
            } catch (\Exception $e) {
                $attributesToSave[$attributeCode] = null;
                throw $e;
            }
        }
        $banner->setData('attributes', $attributesToSave);
        return $this;
    }

    /**
     * @param BannerModel $banner
     * @param string $key
     * @return mixed
     */
    protected function _setValue(BannerModel $banner, $key)
    {
        return $banner->getData($key);
    }

    /**
     * @param BannerModel $banner
     * @param string $key
     * @return null
     */
    protected function _setAttribute(BannerModel $banner, $key)
    {
        $attributeModel = $this->_getAttributeModel($key);
        return $attributeModel ? $attributeModel->setAttribute($banner) : null;
    }

    /**
     * @param string $key
     * @return AttributesInterface|null
     */
    protected function _getAttributeModel($key)
    {
        $attributeObject = $this->attributesConfig->getTargetTypeObject($key);
        if (!($attributeObject instanceof AttributesInterface)) {
            return null;
        }
        return $attributeObject;
    }

    /**
     * @param BannerModel $banner
     * @return array
     */
    public function getBannerAttributes(BannerModel $banner)
    {
        $bannerId = $banner->getId();
        if ($bannerId && isset($this->bannerAttributesByBannerId[$bannerId])) {
            return $this->bannerAttributesByBannerId[$bannerId];
        }
        $attributes = $banner->getCustomAttributes();
        $customAttributes = [];
        if (!empty($attributes)) {
            foreach ($attributes as $attributeCode => $attributeValue) {
                $customAttributes[$attributeCode] = $this->_getAttributeModel($attributeCode) ?
                    $this->_getAttributeModel($attributeCode)->getFrontendAttribute($banner)
                    : $attributeValue;
            }
        }
        $customAttributes['title'] = $banner->getName();
        $this->bannerAttributesByBannerId[$bannerId] = $customAttributes;
        return $customAttributes;
    }

    /**
     * @param BannerModel $banner
     * @return mixed
     */
    public function getBannerAttributesDuringLoad(BannerModel $banner)
    {
        if (!isset($this->bannerAttributesDuringLoadById[$banner->getId()])) {
            $this->bannerAttributesDuringLoadById[$banner->getId()] = $this->_resource->getBannerAttributesById(
                $banner->getId()
            );
        }
        return $this->bannerAttributesDuringLoadById[$banner->getId()];
    }

    /**
     * @param int $bannerId
     * @return bool
     */
    public function isAvailableForCustomerSegment($bannerId)
    {
        /**
         * If null - means that we did not query database yet
         * Then if customer segments restriction is not in use - set it to empty array and do not query again
         */
        if ($this->needToFetchSegments()) {
            try {
                $customerSegmentsCache = $this->getCachedSegmentsInfo();
                if ($customerSegmentsCache === false) {
                    $result = $this->fetchSegmentsInfoFromDb();
                    $this->saveSegmentsMapping($result);
                }
            } catch (\Throwable $exception) {
                $this->bannerCustomerSegment = [];
                return true;
            }
        }

        $allowedSegmentIds = $this->getByKey($this->bannerCustomerSegment, $bannerId, []);
        if (empty($allowedSegmentIds)) {
            return true;
        }

        $segmentIds = $this->customerSegment->getCustomerSegment();
        if (!is_array($segmentIds)) {
            $segmentIds = [$segmentIds];
        }

        if (!$this->hasSegmentRestriction($bannerId)) {
            return true;
        }
        if (is_array($segmentIds)) {

            /**
             * If banner has segmentation restriction but there are not customer segments information
             * return false.
             */
            if (empty($segmentIds)) {
                return false;
            }
            foreach ($segmentIds as $segmentId) {
                if (in_array($segmentId, $allowedSegmentIds)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param int $bannerId
     * @return bool
     */
    protected function hasSegmentRestriction($bannerId)
    {
        return array_key_exists($bannerId, $this->bannerCustomerSegment);
    }

    /**
     * @return bool
     */
    protected function needToFetchSegments()
    {
        return null === $this->bannerCustomerSegment;
    }

    /**
     * @return bool|string
     */
    protected function getCachedSegmentsInfo()
    {
        return $this->cache->load(self::CUSTOMER_SEGMENT_CACHE);
    }

    /**
     * @return array
     */
    protected function fetchSegmentsInfoFromDb()
    {
        return $this->customerSegment->getDbBannerSegments();
    }

    /**
     * @param mixed $result
     */
    protected function saveSegmentsMapping($result)
    {
        if (!empty($result) && is_array($result)) {
            foreach ($result as $index => $information) {
                $bannerIdFromDb = $this->getByKey($information, 'banner_id', null);
                $segmentIdFromDb = $this->getByKey($information, 'segment_id', null);
                if (!$bannerIdFromDb) {
                    continue;
                }
                $this->bannerCustomerSegment[$bannerIdFromDb][] = $segmentIdFromDb;
            }
        } else {
            $this->bannerCustomerSegment = [];
        }
    }

    /**
     * @return void
     */
    protected function saveSegmentsMappingToCache()
    {
        $this->cache->save(
            json_encode($this->bannerCustomerSegment),
            self::CUSTOMER_SEGMENT_CACHE,
            [Cache::CACHE_TAG, Cache::TYPE_IDENTIFIER]
        );
    }
}
