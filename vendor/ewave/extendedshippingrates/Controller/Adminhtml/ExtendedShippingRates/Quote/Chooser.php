<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote;

class Chooser extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote
{
    /**
     * Chooser source action
     *
     * @return void
     */
    public function execute()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $chooserBlock = $this->_view->getLayout()->createBlock(
            'Ewave\ExtendedShippingRates\Block\Adminhtml\ExtendedShippingRates\Widget\Chooser',
            '',
            ['data' => ['id' => $uniqId]]
        );
        $this->getResponse()->setBody($chooserBlock->toHtml());
    }
}
