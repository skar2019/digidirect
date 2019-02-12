<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory;

use Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory;

/**
 * Class Index
 * @package Ewave\ProductCalculator\Controller\Adminhtml\FieldGroupCategory
 */
class Index extends FieldGroupCategory
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend(__('Field Group Categories'));
        return $resultPage;
    }
}
