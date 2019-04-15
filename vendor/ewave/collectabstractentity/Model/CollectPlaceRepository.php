<?php

namespace Ewave\CollectAbstractEntity\Model;

use Ewave\CollectAbstractEntity\Api\Data\CollectFields\Constants;
use Ewave\CollectAbstractEntity\Helper\Config;
use Ewave\Collect\Api\CollectPlaceRepositoryInterface;
use Ewave\AbstractEntity\Api\AbstractEntityRepositoryInterface;
use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\CollectAbstractEntity\Model\ResourceModel\AbstractEntity as AbstractEntityResource;
use Magento\Eav\Model\ResourceModel\AttributeLoader;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;

/**
 * Class CollectPlaceRepository
 * @package Ewave\CollectAbstractEntity\Model
 */
class CollectPlaceRepository implements CollectPlaceRepositoryInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var AbstractEntityRepositoryInterface
     */
    protected $abstractEntityRepository;

    /**
     * @var AbstractEntityResource
     */
    protected $abstractEntityResource;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $loadAttributes;

    /**
     * CollectPlaceRepository constructor.
     * @param Config $configHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AbstractEntityRepositoryInterface $abstractEntityRepository
     * @param AbstractEntityResource $abstractEntityResource
     * @param LoggerInterface $logger
     * @param array $loadAttributes
     */
    public function __construct(
        Config $configHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AbstractEntityRepositoryInterface $abstractEntityRepository,
        AbstractEntityResource $abstractEntityResource,
        LoggerInterface $logger,
        array $loadAttributes = []
    ) {
        $this->configHelper = $configHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->abstractEntityRepository = $abstractEntityRepository;
        $this->abstractEntityResource = $abstractEntityResource;
        $this->logger = $logger;
        $this->loadAttributes = $loadAttributes;
    }

    /**
     * Get Collect Place by Id
     *
     * @param string $id
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface
     */
    public function getById($id)
    {
        try {
            /** @var AbstractEntityCollectPlace $entity */
            $entity = $this->abstractEntityRepository->getById($id);
        } catch (\Throwable $exception) {
            $this->logger->error(
                __("Can't get an Abstract Entity row with id {$id}.") . $exception->getMessage()
            );
            return false;
        }
        return $entity;
    }

    /**
     * @return array
     */
    public function getAttributesToLoad()
    {
        $map = $this->configHelper->getCollectFieldsMatrix();
        $attributesToLoad = array_merge(
            [
                $map[Constants::COLLECT_FIELD_POSTCODE],
                $map[Constants::COLLECT_FIELD_LONGITUDE],
                $map[Constants::COLLECT_FIELD_LATITUDE],
                $map[Constants::COLLECT_FIELD_NAME],
                $map[Constants::COLLECT_FIELD_ADDRESS]
            ],
            array_values($this->loadAttributes)
        );
        $attributesToLoad = array_unique($attributesToLoad);
        return $attributesToLoad;
    }

    /**
     * @return SearchCriteriaBuilder
     */
    public function buildAllSearchCriteria()
    {
        $setId = $this->configHelper->getCollectAbstractEntityId();
        $this->searchCriteriaBuilder->addFilter(AttributeLoader::ATTRIBUTE_SET_ID, $setId);

        if ($this->abstractEntityResource->isAttributeInAttributeSet($setId, AbstractEntityInterface::STATUS)) {
            $this->searchCriteriaBuilder->addFilter(
                \Ewave\AbstractEntity\Api\Data\AbstractEntityInterface::STATUS,
                \Ewave\AbstractEntity\Model\AbstractEntity\Attribute\Source\Status::STATUS_DISABLED,
                'neq'
            );
        }

        return $this->searchCriteriaBuilder;
    }

    /**
     * Get All CollectPlaces
     *
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface[]
     */
    public function getAll()
    {
        $setId = $this->configHelper->getCollectAbstractEntityId();
        $map = $this->configHelper->getCollectFieldsMatrix();
        if (!$setId || !$map) {
            return [];
        }

        $searchCriteriaBuilder = $this->buildAllSearchCriteria();
        $criteria = $searchCriteriaBuilder->create();
        try {
            $setName = $this->abstractEntityResource->getAttributeSetNameById($setId);
            $collection = $this->abstractEntityRepository->getList($criteria, $setName, $this->getAttributesToLoad());
        } catch (\Throwable $e) {
            $this->logger->error(
                __("Can't get a collection of entities with Attribute Set {$setId}.") . $e->getMessage()
            );
            return [];
        }
        return $collection->getItems();
    }

    /**
     * Get collect places list by SKU
     *
     * @param string $sku
     * @param int $qty
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getListBySku($sku, $qty = 1)
    {
        return $this->getAll();
    }

    /**
     * Get collect places list by SKU
     *
     * @param array $skus
     * @param int $qty
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getListBySkus($skus, $qty = 1)
    {
        $items = $this->getAll();
        return $this->filterListBySkus($items, $skus, $qty);
    }

    /**
     * @param array $places
     * @param array $skus
     * @param int|array $qty
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function filterListBySkus(array $places, array $skus, $qty = 1)
    {
        // @todo For the future Stock implementations
        // @see Ewave\CollectAbstractEntity\Model\CollectPlaceStock
        return $places;
    }
}
