<?php

namespace Ewave\Navigation\Controller\Adminhtml\Menu;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * Class NewAction
 * @package Ewave\Navigation\Controller\Adminhtml\Menu
 */
class NewAction extends Action
{
    /**
     * @var PageFactory $_resultPageFactory
     */
    protected $resultForwardFactory;

    /**
     * Edit constructor.
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(Context $context, ForwardFactory $resultForwardFactory)
    {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
