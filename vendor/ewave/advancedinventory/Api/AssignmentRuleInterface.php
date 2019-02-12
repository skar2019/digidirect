<?php
namespace Ewave\AdvancedInventory\Api;

interface AssignmentRuleInterface
{
    /**
     * @param int|null $scopeId
     * @return int
     */
    public function getStockId($scopeId = null);
}
