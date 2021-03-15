<?php

namespace Digidirect\MyOrderItemsGroups\Api;

/**
 * Interface ItemGroupLinkManagementInterface
 * @package Digidirect\MyOrderItemsGroups\Api
 */
interface ItemGroupLinkManagementInterface
{
    /**
     * @param int $salesItemId
     * @param int $groupId
     * @param null $position
     * @return mixed
     */
    public function setItemGroupLink($salesItemId, $groupId, $position = null);
}
