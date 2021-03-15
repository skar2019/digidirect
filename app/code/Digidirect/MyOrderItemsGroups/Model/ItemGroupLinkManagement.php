<?php

namespace Digidirect\MyOrderItemsGroups\Model;

use Magento\Framework\Exception\LocalizedException;
use Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroupLink as OrderItemGroupLinkResource;
use Digidirect\MyOrderItemsGroups\Api\ItemGroupLinkManagementInterface;

/**
 * Class OrderItemGroupRepository
 * @package Digidirect\MyOrderItemsGroups\Model
 */
class ItemGroupLinkManagement implements ItemGroupLinkManagementInterface
{
    /**
     * @var OrderItemGroupLinkResource
     */
    protected $resourceModel;

    /**
     * ItemGroupLinkManagement constructor.
     * @param OrderItemGroupLinkResource $resourceModel
     */
    public function __construct(OrderItemGroupLinkResource $resourceModel)
    {
        $this->resourceModel = $resourceModel;
    }

    /**
     * @param int $salesItemId
     * @param int $groupId
     * @param mixed $position
     * @return mixed|void
     * @throws LocalizedException
     */
    public function setItemGroupLink($salesItemId, $groupId, $position = null)
    {
        $this->resourceModel->insertLink($salesItemId, $groupId, $position);
    }

    /**
     * @param $groupId
     */
    public function shiftItems($groupId)
    {
        $this->resourceModel->shiftItems($groupId);
    }

    /**
     * @param $groupId
     * @return int
     * @throws LocalizedException
     */
    public function getMinPosition($groupId)
    {
        return $this->resourceModel->getMinPosition($groupId);
    }

    /**
     * @param $groupId
     * @param array $itemIds
     * @throws LocalizedException
     */
    public function deleteLinks($groupId, array $itemIds = [])
    {
        $this->resourceModel->deleteLinks($groupId, $itemIds);
    }

    /**
     * @param $groupId
     * @param $itemId
     * @return bool
     */
    public function checkRelation($groupId, $itemId)
    {
        try {
            return (bool)$this->resourceModel->checkRelation($groupId, $itemId);
        } catch (LocalizedException $exception) {
            return false;
        }
    }
}
