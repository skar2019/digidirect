<?php

namespace Digidirect\MyOrderItemsGroups\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Magento\Sales\Api\Data\OrderItemSearchResultInterfaceFactory;
use Psr\Log\LoggerInterface;
use Digidirect\MyOrderItemsGroups\Api\ItemGroupLinkRepositoryInterface;
use Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroupLink as ItemGroupLinkResource;

/**
 * Class ItemGroupLinkRepository
 * @package Digidirect\MyOrderItemsGroups\Model
 */
class ItemGroupLinkRepository implements ItemGroupLinkRepositoryInterface
{
    /**
     * @var ItemGroupLinkResource
     */
    protected $resourceModel;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var OrderItemSearchResultInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ItemGroupLinkRepository constructor.
     * @param ItemGroupLinkResource $resourceModel
     * @param CollectionProcessorInterface $collectionProcessor
     * @param OrderItemSearchResultInterfaceFactory $searchResultFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ItemGroupLinkResource $resourceModel,
        CollectionProcessorInterface $collectionProcessor,
        OrderItemSearchResultInterfaceFactory $searchResultFactory,
        LoggerInterface $logger
    ) {
        $this->resourceModel = $resourceModel;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
        $this->logger = $logger;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param null|int $customerId
     * @return OrderItemCollection
     * @throws LocalizedException
     */
    public function getOrderItemList(SearchCriteriaInterface $searchCriteria, $customerId = null)
    {
        /** @var OrderItemCollection $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $this->resourceModel->joinExtraData($searchResult, $customerId);
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        return $searchResult;
    }

    /**
     * @param $groupId
     * @return array
     */
    public function getProductIds($groupId)
    {
        try {
            return $this->resourceModel->getProductIds($groupId);
        } catch (LocalizedException $exception) {
            return [];
        }
    }

    /**
     * @param OrderItemCollection $collection
     * @param $groupId
     */
    public function filterGroups(OrderItemCollection $collection, $groupId)
    {
        $this->resourceModel->filterGroups($collection, $groupId);
    }
}
