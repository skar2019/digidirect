<?php
namespace Ewave\OutOfStockNotif\Controller\Add;

use Ewave\OutOfStockNotif\Helper\Data as Helper;
use Ewave\OutOfStockNotif\Model\OutOfStockRepository;
use Magento\ProductAlert\Controller\Add\Stock as MagentoStock;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Stock extends MagentoStock
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var OutOfStockRepository
     */
    protected $outOfStockRepository;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param OutOfStockRepository $outOfStockRepository
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        OutOfStockRepository $outOfStockRepository,
        Helper $helper
    ) {
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->outOfStockRepository = $outOfStockRepository;
        $this->helper = $helper;
        parent::__construct(
            $context,
            $customerSession,
            $productRepository
        );
    }

    /**
     * Check customer authentication for some actions
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->helper->isEnabledForGuest()) {
            return parent::dispatch($request);
        }
        return Action::dispatch($request);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $backUrl = $this->getRequest()->getParam(Action::PARAM_NAME_URL_ENCODED);
        $customerId = $this->customerSession->isLoggedIn() ? $this->customerSession->getCustomerId() : null;
        $productId = (int)$this->getRequest()->getParam('product_id');
        $email = $this->getRequest()->getParam('email');
        $websiteId = $this->storeManager->getWebsite()->getId();

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$backUrl || !$productId || !$email) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        try {
            if ($this->outOfStockRepository->subscribe($email, $productId, $websiteId, $customerId)) {
                $this->messageManager->addSuccessMessage(__('Alert subscription has been saved.'));
            } else {
                $this->messageManager->addSuccessMessage(__('Thank you! You are already subscribed to this product.'));
            }
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t update the alert subscription right now.'));
        }
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }
}
