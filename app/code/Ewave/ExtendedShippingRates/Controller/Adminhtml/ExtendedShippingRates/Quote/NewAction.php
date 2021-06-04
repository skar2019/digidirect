<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote;

class NewAction extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote
{
    /**
     * New shipping rule action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
