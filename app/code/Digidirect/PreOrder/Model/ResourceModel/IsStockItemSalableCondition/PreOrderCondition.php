<?php

namespace Digidirect\PreOrder\Model\ResourceModel\IsStockItemSalableCondition;

use Magento\Framework\DB\Select;
use Magento\InventorySales\Model\ResourceModel\IsStockItemSalableCondition\GetIsStockItemSalableConditionInterface;
use Digidirect\PreOrder\Helper\Data as PreOrderHelper;

/**
 * Class PreOrderCondition
 * @package Digidirect\PreOrder\Model\ResourceModel\IsStockItemSalableCondition
 */
class PreOrderCondition implements GetIsStockItemSalableConditionInterface
{
    /**
     * @var PreOrderHelper
     */
    protected $preOrderHelper;

    /**
     * PreOrderCondition constructor.
     * @param PreOrderHelper $preOrderHelper
     */
    public function __construct(PreOrderHelper $preOrderHelper)
    {
        $this->preOrderHelper = $preOrderHelper;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Select $select): string
    {
        $condition = '';
        $isPreOrderEnabled = $this->preOrderHelper->getConfig()->preordersEnabled();
        $isAllowEmptyQty = $this->preOrderHelper->getConfig()->isAllowEmptyQty();

        if ($isPreOrderEnabled && $isAllowEmptyQty) {
            $condition = '(legacy_stock_item.backorders = ' . PreOrderHelper::BACKORDERS_PREORDER_OPTION .
                ' AND legacy_stock_item.is_in_stock = 1 AND legacy_stock_item.qty <= 0)';
        }

        return $condition;
    }
}
