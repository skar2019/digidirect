<?php
namespace Ewave\Banner\Controller\Adminhtml\Widget;

use Magento\Banner\Controller\Adminhtml\Banner\Widget\Chooser as OriginalChooser;
use Ewave\Banner\Block\Adminhtml\Widget\Chooser as ChooserBlock;

/**
 * Class Chooser
 * @package Ewave\Banner\Controller\Adminhtml\Widget
 */
class Chooser extends OriginalChooser
{
    /**
     * Chooser Source action
     * @return void
     */
    public function execute()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');

        $bannersGrid = $this->_view->getLayout()->createBlock(
            ChooserBlock::class,
            '',
            ['data' => ['id' => $uniqId]]
        );
        $html = $bannersGrid->toHtml();

        $this->getResponse()->setBody($html);
    }
}
