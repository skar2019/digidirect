<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote;

class Index extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Ewave Shipping Rules'));
        $this->_view->renderLayout();
    }
}
