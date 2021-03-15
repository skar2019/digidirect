<?php

namespace Digidirect\MyOrderItems\Controller\MyOrderItems;

use Digidirect\MyOrderItems\Controller\MyOrderItems;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;

/**
 * Class Index
 * @package Digidirect\MyOrderItems\Controller\MyOrderItems
 */
class Index extends MyOrderItems
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $customerSession);
    }

    /**
     * Show my order items
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $page = $this->resultPageFactory->create();
        $page->getConfig()->getTitle()->set(__('My Order Items'));
        return $page;
    }
}