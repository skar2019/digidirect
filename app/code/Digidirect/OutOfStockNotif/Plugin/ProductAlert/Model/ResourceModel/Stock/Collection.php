<?php
namespace Digidirect\OutOfStockNotif\Plugin\ProductAlert\Model\ResourceModel\Stock;

use Magento\ProductAlert\Model\ResourceModel\Stock\Collection as StockCollection;

class Collection
{
    /**
     * Remove guests from standard collection
     *
     * @param StockCollection $subject
     * @return void
     */
    public function beforeSetCustomerOrder(
        StockCollection $subject
    ) {
        $subject->getSelect()->where(new \Zend_Db_Expr('customer_id > 0'));
    }
}
