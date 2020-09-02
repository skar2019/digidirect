<?php
namespace Ewave\ExtendedCartPriceRules\Model\ResourceModel\ExtendedCartPriceRule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Ewave\CheckoutFields\Model\OrderFieldValue',
            'Ewave\ExtendedCartPriceRules\Model\ResourceModel\ExtendedCartPriceRule'
        );
    }
}
