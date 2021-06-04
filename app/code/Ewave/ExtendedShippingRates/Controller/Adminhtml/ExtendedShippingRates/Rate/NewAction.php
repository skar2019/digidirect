<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Rate;

class NewAction extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Rate
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
