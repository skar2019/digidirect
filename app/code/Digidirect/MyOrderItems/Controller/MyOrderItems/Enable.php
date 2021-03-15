<?php

namespace Digidirect\MyOrderItems\Controller\MyOrderItems;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Digidirect\MyOrderItems\Model\OrderItemStateRepository;
use Digidirect\MyOrderItems\Controller\MyOrderItems;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;

/**
 * Class Enable
 * @package Digidirect\MyOrderItems\Controller\MyOrderItems
 */
class Enable extends MyOrderItems
{
    /**
     * Request parameters
     */
    const ITEM_ID = 'item_id';

    /**
     * @var OrderItemStateRepository
     */
    protected $stateRepository;

    /**
     * @var UrlHandlerPool
     */
    protected $urlHandlerPool;

    /**
     * Enable constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param OrderItemStateRepository $stateRepository
     * @param UrlHandlerPool $urlHandlerPool
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        OrderItemStateRepository $stateRepository,
        UrlHandlerPool $urlHandlerPool
    ) {
        $this->stateRepository = $stateRepository;
        $this->urlHandlerPool = $urlHandlerPool;
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
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam(self::ITEM_ID);
        $customerId = $this->customerSession->getCustomerId();
        try {
            if ($this->stateRepository->checkItemPermission($id, $customerId)) {
                $this->stateRepository->enableOrderItem($id, $customerId);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('Item has not been enabled.'));
        }
        return $resultRedirect->setPath('*/*/index', $this->collectParams());
    }

    /**
     * @param array $exclude
     * @return array
     */
    protected function collectParams($exclude = [])
    {
        $exclude[] = self::ITEM_ID;
        return $this->urlHandlerPool->execute($this->getRequest(), $exclude);
    }
}
