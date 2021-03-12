<?php
namespace Digidirect\OutOfStockNotif\Controller\Unsubscribe;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class Stock extends Action
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CollectionFactory
     */
    protected $stockCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param CollectionFactory $stockCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $stockCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->productRepository = $productRepository;
        $this->stockCollectionFactory = $stockCollectionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        $email = $this->getRequest()->getParam('email');

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$productId || !$email) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        try {
            $product = $this->productRepository->getById($productId);
            if (!$product->isVisibleInCatalog()) {
                throw new NoSuchEntityException(__('Product does not exist.'));
            }

            /** @var \Magento\ProductAlert\Model\ResourceModel\Stock\Collection $collection */
            $collection = $this->stockCollectionFactory->create()
                ->addFieldToFilter('email', $email)
                ->addFieldToFilter('product_id', $product->getId())
                ->addFieldToFilter('website_id', $this->storeManager->getWebsite()->getId());

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->messageManager->addSuccessMessage(__('You will no longer receive stock alerts for this product.'));
        } catch (NoSuchEntityException $noEntityException) {
            $this->messageManager->addErrorMessage(__('The product was not found.'));
            $resultRedirect->setPath('/');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t update the alert subscription right now.'));
        }

        $resultRedirect->setUrl($product->getProductUrl());
        return $resultRedirect;
    }
}
