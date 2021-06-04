<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Zone;

class NewAction extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Zone
{
    /**
     * New shipping zone action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
