<?php

namespace Digidirect\MyOrderItemsGroups\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Psr\Log\LoggerInterface;
use Digidirect\MyOrderItemsGroups\Api\OrderItemGroupRepositoryInterface;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupSearchResultInterface;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupSearchResultInterfaceFactory;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterfaceFactory;
use Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroup as OrderItemGroupResource;
use Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroup\CollectionFactory as GroupCollectionFactory;

/**
 * Class OrderItemGroupRepository
 * @package Digidirect\MyOrderItemsGroups\Model
 */
class OrderItemGroupRepository implements OrderItemGroupRepositoryInterface
{
    /**
     * @var OrderItemGroupInterfaceFactory
     */
    protected $orderItemGroupFactory;

    /**
     * @var OrderItemGroupResource
     */
    protected $resourceModel;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var GroupCollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var OrderItemGroupSearchResultInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * OrderItemGroupRepository constructor.
     * @param OrderItemGroupInterfaceFactory $orderItemGroupFactory
     * @param OrderItemGroupResource $resourceModel
     * @param CollectionProcessorInterface $collectionProcessor
     * @param \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderItemGroupInterfaceFactory $orderItemGroupFactory,
        OrderItemGroupResource $resourceModel,
        CollectionProcessorInterface $collectionProcessor,
        \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultFactory,
        GroupCollectionFactory $groupCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->orderItemGroupFactory = $orderItemGroupFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * @param int $id
     * @return OrderItemGroupInterface
     */
    public function getById($id)
    {
        $itemGroup = $this->orderItemGroupFactory->create();
        $this->resourceModel->load($itemGroup, $id);
        if (!$itemGroup->getGroupId()) {
            throw new NoSuchEntityException(__('The group is not loaded'));
        }
        return $itemGroup;
    }

    /**
     * @param OrderItemGroupInterface $orderItemGroup
     * @return mixed|void
     * @throws CouldNotSaveException
     */
    public function save(OrderItemGroupInterface $orderItemGroup)
    {
        try {
            /** @var OrderItemGroupInterface $orderItemGroup */
            $this->resourceModel->save($orderItemGroup);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Order item group has not been saved'));
        }
    }

    /**
     * @param OrderItemGroupInterface $orderItemGroup
     * @return mixed|void
     * @throws CouldNotDeleteException
     */
    public function delete(OrderItemGroupInterface $orderItemGroup)
    {
        try {
            /** @var OrderItemGroupInterface $orderItemGroup */
            $this->resourceModel->delete($orderItemGroup);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Order item group has not been deleted'));
        }
    }

    /**
     * @param $groupId
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($groupId)
    {
        $group = $this->getById($groupId);
        $this->delete($group);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    public function getOrderItemGroupList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->groupCollectionFactory->create();
        /** @var \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setItems($collection->getItems());
        return $searchResult;
    }

    /**
     * @param $customerId
     * @param $groupId
     * @return bool
     */
    public function checkGroupForCustomer($customerId, $groupId)
    {
        try {
            return (bool)$this->resourceModel->checkGroupForCustomer($customerId, $groupId);
        } catch (LocalizedException $exception) {
            return false;
        }
    }

    /**
     * @param $customerId
     * @return int
     * @throws LocalizedException
     */
    public function getCurrentGroup($customerId)
    {
        return $this->resourceModel->getCurrentGroup($customerId);
    }
}
