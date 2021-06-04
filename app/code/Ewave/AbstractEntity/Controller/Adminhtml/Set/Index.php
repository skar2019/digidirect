<?php
namespace Ewave\AbstractEntity\Controller\Adminhtml\Set;

use Ewave\AbstractEntity\Controller\Adminhtml\Set;
use Ewave\AbstractEntity\Model\ResourceModel\AbstractEntity as AbstractEntityResource;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Index extends Set
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param AbstractEntityResource $abstractEntityResource
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        AbstractEntityResource $abstractEntityResource,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $coreRegistry, $abstractEntityResource);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_setTypeId();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ewave_AbstractEntity::abstractentity_view');
        $resultPage->getConfig()->getTitle()->prepend(__('Abstract Entities'));
        $resultPage->addBreadcrumb(__('Ewave'), __('Abstract Entities'));
        $resultPage->addBreadcrumb(__('Manage Abstract Entities'), __('Abstract Entities'));
        return $resultPage;
    }
}
