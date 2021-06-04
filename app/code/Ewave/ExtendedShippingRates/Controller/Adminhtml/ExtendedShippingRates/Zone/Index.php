<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Zone;

class Index extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Zone
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Shipping Zones'));
        $this->_view->renderLayout('root');
    }
}
