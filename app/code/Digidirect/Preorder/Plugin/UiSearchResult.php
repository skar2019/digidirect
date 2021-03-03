<?php

namespace Digidirect\PreOrder\Plugin;

use Digidirect\PreOrder\Model\ResourceModel\OrderPreorder;
use Magento\Framework\DB\Select;

/**
 * Class UiSearchResult
 *
 * @package Digidirect\PreOrder\Plugin
 */
class UiSearchResult
{
    /**
     * Before load method plugin
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $subject
     * @return void
     */
    public function beforeLoad(\Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $subject)
    {
        if (false !== strpos($subject->getMainTable(), 'sales_order_grid')) {
            $this->injectSelect($subject);
            return;
        }
    }

    /**
     * Before getSelectCountSql method plugin
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $subject
     * @return void
     */
    public function beforeGetSelectCountSql(
        \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $subject
    ) {
        if (false !== strpos($subject->getMainTable(), 'sales_order_grid')) {
            $this->injectSelect($subject);
        }
    }

    /**
     * Inject select
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $subject
     * @return void
     */
    protected function injectSelect(\Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $subject)
    {
        $select = $subject->getSelect();
        if (false === strpos((string)$select, OrderPreorder::TABLE)) {
            $select->joinLeft(
                ['preorder' => $subject->getTable(OrderPreorder::TABLE)],
                'preorder.order_id = main_table.entity_id',
                [
                    'is_preorder' => new \Zend_Db_Expr(
                        "IF(preorder.is_preorder IS NULL, 0, preorder.is_preorder)"
                    )
                ]
            );
        }
        $where = $select->getPart(Select::WHERE);
        foreach ($where as &$part) {
            if (false !== strpos($part, '`is_preorder` = \'0\'')) {
                $part = str_replace("= '0'", 'IS NULL', $part);
            }
        }
        $select->setPart(Select::WHERE, $where);
    }
}
