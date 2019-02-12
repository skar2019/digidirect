<?php

namespace Ewave\Navigation\Controller\Adminhtml\Menu;

use \Magento\Backend\App\Action\Context;
use \Magento\Backend\App\Action;
use \Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(Context $context, PageFactory $pageFactory)
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Ewave_Navigation::ewave');
        $resultPage->addBreadcrumb(__('Menu Management'), __('Menu Management'));
        $resultPage->getConfig()->getTitle()->prepend(__('Menu Management'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_Navigation::navigation');
    }
}
