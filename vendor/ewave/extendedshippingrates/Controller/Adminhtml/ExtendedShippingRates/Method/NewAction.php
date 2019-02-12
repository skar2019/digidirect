<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Method;

class NewAction extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Method
{
    /**
     * New action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
