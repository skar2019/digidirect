<?php
namespace Ewave\AdvancedInventoryMyStore\Model\StockResolver\AssignmentRule;

use Ewave\AdvancedInventory\Api\AssignmentRuleInterface;
use Ewave\AdvancedInventory\Model\ResourceModel\AdvancedInventoryStock;
use Ewave\AdvancedInventoryMyStore\Plugin\MyStoreWidget\Model\ResourceModel\MyStoreIndex;
use Ewave\MyStoreWidget\Helper\Data as MyStoreHelper;

class MyStore implements AssignmentRuleInterface
{
    /**
     * @var AdvancedInventoryStock
     */
    protected $advancedInventoryStock;

    /**
     * @var MyStoreHelper
     */
    protected $myStoreHelper;

    /**
     * @param AdvancedInventoryStock $advancedInventoryStock
     * @param MyStoreHelper $myStoreHelper
     */
    public function __construct(
        AdvancedInventoryStock $advancedInventoryStock,
        MyStoreHelper $myStoreHelper
    ) {
        $this->advancedInventoryStock = $advancedInventoryStock;
        $this->myStoreHelper = $myStoreHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockId($scopeId = null)
    {
        $currentStore = $this->myStoreHelper->getCurrentStore(false, MyStoreIndex::ADVANCED_INVENTORY_ENTITY_TYPE);
        if (!$currentStore) {
            return false;
        }

        $stockId = $this->advancedInventoryStock->getStockIdByEntityId($currentStore->getId());
        if (!$stockId && $currentStore->getParentId()) {
            $stockId = $this->advancedInventoryStock->getStockIdByEntityId($currentStore->getParentId());
        }

        return $stockId;
    }
}
