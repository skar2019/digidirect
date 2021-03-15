<?php

namespace Digidirect\MyOrderItemsGroups\Controller\MyOrderItems;

use Digidirect\MyOrderItems\Controller\MyOrderItems;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\ForwardFactory;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface;

/**
 * Class Share
 * @package Digidirect\MyOrderItemsGroups\Controller\MyOrderItems
 */
class Share extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Share constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        if (!$this->getRequest()->getParam(OrderItemGroupInterface::GROUP_ID)) {
            return $resultForward->forward('noroute');
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $page = $this->resultPageFactory->create();
        $page->getConfig()->getTitle()->set(__('Share Group Order Items'));
        return $page;
    }
}
