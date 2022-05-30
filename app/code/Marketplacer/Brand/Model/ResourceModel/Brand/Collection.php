<?php

namespace Marketplacer\Brand\Model\ResourceModel\Brand;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Api\StoreRepositoryInterface;
use Marketplacer\Base\Model\Attribute\AttributeOptionHandler;
use Marketplacer\Base\Model\ResourceModel\AbstractCollection;
use Marketplacer\Brand\Api\Data\BrandCollectionInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use Psr\Log\LoggerInterface;

//use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection implements BrandCollectionInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplacer_brand_collection';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'collection';

    /**
     * @var string
     */
    protected $_idEntityKey = 'brand_id';

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var AttributeOptionHandler
     */
    protected $attributeOptionHandler;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriever;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreRepositoryInterface $store
     * @param string $eventPrefix
     * @param string $eventObject
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreRepositoryInterface $storeRepository,
        AttributeOptionHandler $attributeOptionHandler,
        BrandAttributeRetrieverInterface $brandAttributeRetriever,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->storeRepository = $storeRepository;
        $this->attributeOptionHandler = $attributeOptionHandler;
        $this->brandAttributeRetriever = $brandAttributeRetriever;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );

        $this->addFilterToMap('id', 'main_table.brand_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Marketplacer\Brand\Model\Brand', 'Marketplacer\Brand\Model\ResourceModel\Brand');
    }

    /**
     * @param mixed $brandId
     * @return $this
     */
    public function addBrandIdToFilter($brandId)
    {
        $this->addFieldToFilter(BrandInterface::BRAND_ID, $brandId);
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function addBrandNameToFilter($name)
    {
        $this->addFieldToFilter(BrandInterface::NAME, $name);
        return $this;
    }

    /**
     * @return $this
     */
    public function addWithNameToFilter()
    {
        $this->addFieldToFilter(BrandInterface::NAME, 'notnull');
        return $this;
    }

    /**
     * @return $this
     */
    public function addStatusToFilter($status)
    {
        $this->addFieldToFilter(BrandInterface::STATUS, $status);
        return $this;
    }

    /**
     * @return $this
     */
    public function addStatusActiveToFilter()
    {
        $this->addStatusToFilter(BrandInterface::STATUS_ENABLED);
        return $this;
    }

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    protected function _afterLoad()
    {
        $this->initBrandAttributeOptions();

        return parent::_afterLoad();
    }

    /**
     * @return $this
     * @throws NoSuchEntityException
     */
    protected function initBrandAttributeOptions()
    {
        $brands = $this->getItems();
        $optionIds = array_map(
            function (BrandInterface $brand) {
                return $brand->getOptionId();
            },
            $brands
        );

        $optionIds = array_filter(array_unique($optionIds));
        $brandAttribute = $this->brandAttributeRetriever->getAttribute();
        $indexedOptions = $this->attributeOptionHandler->getAttributeOptionsByIds($brandAttribute, $optionIds);

        foreach ($brands as $brand) {
            $option = $indexedOptions[$brand->getOptionId()] ?? null;
            if ($option) {
                $brand->setAttributeOption($option);
            }
        }

        return $this;
    }
}
