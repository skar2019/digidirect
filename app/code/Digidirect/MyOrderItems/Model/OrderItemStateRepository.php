<?php

namespace Digidirect\MyOrderItems\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Sales\Api\Data\OrderItemSearchResultInterfaceFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Psr\Log\LoggerInterface;
use Digidirect\MyOrderItems\Api\OrderItemStateRepositoryInterface;
use Digidirect\MyOrderItems\Model\ResourceModel\OrderItemState as OrderItemStateResource;
use Digidirect\MyOrderItems\Model\OrderItemState;
use Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface;

/**
 * Class OrderItemStateRepository
 * @package Digidirect\MyOrderItems\Model
 */
class OrderItemStateRepository implements OrderItemStateRepositoryInterface
{
    /**
     * @var OrderItemStateFactory
     */
    protected $orderItemStateFactory;

    /**
     * @var OrderItemStateResource
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
     * OrderItemStateRepository constructor.
     * @param OrderItemStateFactory $orderItemStateFactory
     * @param OrderItemStateResource $resourceModel
     * @param CollectionProcessorInterface $collectionProcessor
     * @param OrderItemSearchResultInterfaceFactory $searchResultFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderItemStateFactory $orderItemStateFactory,
        OrderItemStateResource $resourceModel,
        CollectionProcessorInterface $collectionProcessor,
        OrderItemSearchResultInterfaceFactory $searchResultFactory,
        LoggerInterface $logger
    ) {
        $this->orderItemStateFactory = $orderItemStateFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
        $this->logger = $logger;
    }

    /**
     * @param $id
     * @return OrderItemState
     */
    public function getById($id)
    {
        $saleItemState = $this->orderItemStateFactory->create();
        $this->resourceModel->load($saleItemState, $id);
        return $saleItemState;
    }

    /**
     * @param OrderItemStateInterface $orderItemState
     * @return mixed|void
     */
    public function save(OrderItemStateInterface $orderItemState, $customerId = null)
    {
        try {
            /** @var OrderItemState $orderItemState */
            $isNewObject = $this->isNewObject($orderItemState, $customerId);
            if ($isNewObject) {
                $this->setLastEnabled($orderItemState, $customerId);
                $this->resourceModel->shiftInterval(
                    $customerId,
                    OrderItemStateResource::RIGHT,
                    [OrderItemStateResource::MIN => $orderItemState->getSortOrder()]
                );
            }
            $this->resourceModel->save($orderItemState);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Order item state has not saved'));
        }
    }

    /**
     * @param OrderItemStateInterface $orderItemState
     * @param $customerId
     * @return bool
     */
    protected function isNewObject(OrderItemStateInterface $orderItemState, $customerId)
    {
        /** @var OrderItemState $orderItemState */
        return $orderItemState->hasObjectNewFlag() && $customerId;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return OrderItemCollection
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
     * @param OrderItemCollection $collection
     * @param $categoryId
     */
    public function filterCategories(OrderItemCollection $collection, $categoryId)
    {
        $this->resourceModel->filterCategories($collection, $categoryId);
    }

    /**
     * @param $orderItemId
     * @param $customerId
     */
    public function disableOrderItem($orderItemId, $customerId)
    {
        $interval = [];
        $orderItemState = $this->getById($orderItemId);
        $interval[OrderItemStateResource::MIN] = $orderItemState->getSortOrder();
        $this->setLastDisabled($orderItemState, $customerId);
        $interval[OrderItemStateResource::MAX] = $orderItemState->getSortOrder();
        $this->resourceModel->shiftInterval($customerId, OrderItemStateResource::LEFT, $interval);
        $this->save($orderItemState);
    }

    /**
     * @param $orderItemId
     * @param $customerId
     */
    public function enableOrderItem($orderItemId, $customerId)
    {
        $orderItemState = $this->getById($orderItemId);
        $interval[OrderItemStateResource::MAX] = $orderItemState->getSortOrder();
        $this->setLastEnabled($orderItemState, $customerId);
        $interval[OrderItemStateResource::MIN] = $orderItemState->getSortOrder();
        $this->resourceModel->shiftInterval($customerId, OrderItemStateResource::RIGHT, $interval);
        $this->save($orderItemState);
    }

    /**
     * @param $orderItemId
     * @param $customerId
     */
    public function checkItemPermission($orderItemId, $customerId)
    {
        if (!$this->resourceModel->checkItemPermission($orderItemId, $customerId)) {
            throw new LocalizedException(__('You have no permission to update this item'));
        }
        return true;
    }

    /**
     * @param OrderItemStateInterface $orderItemState
     * @param $customerId
     */
    protected function setLastEnabled(OrderItemStateInterface $orderItemState, $customerId)
    {
        $maxSortOrder = $this->resourceModel->getMaxSortOrder($customerId, true);
        $orderItemState->setSortOrder($maxSortOrder + 1);
        $orderItemState->setStatus(OrderItemStateInterface::ENABLED);
    }

    /**
     * @param OrderItemStateInterface $orderItemState
     * @param $customerId
     */
    protected function setLastDisabled(OrderItemStateInterface $orderItemState, $customerId)
    {
        $maxSortOrder = $this->resourceModel->getMaxSortOrder($customerId);
        $orderItemState->setSortOrder($maxSortOrder);
        $orderItemState->setStatus(OrderItemStateInterface::DISABLED);
    }
}
