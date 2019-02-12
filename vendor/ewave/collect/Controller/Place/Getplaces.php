<?php

namespace Ewave\Collect\Controller\Place;

use Ewave\Collect\Controller\AbstractAction;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Webapi\Exception;

/**
 * Class Getplaces
 * @package Ewave\Collect\Controller\Place
 */
class Getplaces extends AbstractAction
{
    /**
     * Product Repository
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * CheckoutSession
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Getplaces constructor.
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Ewave\Collect\Helper\Data $collectHelper
     * @param \Ewave\Collect\Model\StorageHandler $storageHandler
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Ewave\Collect\Helper\Data $collectHelper,
        \Ewave\Collect\Model\StorageHandler $storageHandler,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context, $collectHelper, $storageHandler, $logger);
        $this->_productRepository = $productRepository;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Execute
     *
     * @return $this
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $productSkus = $this->initProductSkus();
            $postcode = $this->getRequest()->getParam('collect_postcode');
            $distance = $this->getRequest()->getParam('collect_distance');
            $qty = $this->getRequest()->getParam('collect_qty', 1);
            $findResult = $this->_storageHandler->getPlacesByData($productSkus, $postcode, $distance, $qty);

        } catch (\Exception $e) {
            $this->_collectHelper->logError($e->getMessage());
            return $resultJson->setData(
                [
                    'data' => [],
                    'raw_data' => [],
                    'result' => 'false',
                    'message' => __('Something went wrong, try to update page and search again')
                ]
            );
        }

        $result = [];
        if ($findResult['catch']) {
            $result = [
                'data' => $findResult['data'],
                'raw_data' => $findResult,
                'result' => true
            ];
        } elseif (!$findResult['catch'] && !empty($findResult['data'])) {
            $result = [
                'data' => $findResult['data'],
                'raw_data' => $findResult,
                'result' => true,
                'catch' => false,
                'message' => __('Your search had no results but we may recommend the following stores')
            ];
        } else {
            $result = [
                'data' => isset($findResult['data']) ? $findResult['data'] : false,
                'raw_data' => $findResult,
                'result' => false,
                'catch' => false,
                'message' => __('The item is no longer available for Click & Collect. Please, select delivery instead.')
            ];
        }

        return $resultJson->setData($result);
    }

    /**
     * Get Product Skus
     *
     * @return null|array
     * @throws \Exception
     */
    protected function initProductSkus()
    {
        $quoteItemId = $this->getRequest()->getParam('quote_item_id');
        $productId = $this->getRequest()->getParam('simple_product_id');
        $result = [];

        if (!$productId) {
            //get product id
            $productId = $this->getRequest()->getParam('product_id');
        }
        if ($quoteItemId) {
            $quoteItem = $this->_checkoutSession->getQuote()->getItemById($quoteItemId);
            if ($quoteItem) {
                $result[] = $quoteItem->getSku();
            }
        } elseif ($productId) {
            try {
                $product = $this->_productRepository->getById($productId);
                $result[] = $product->getSku();
            } catch (\Exception $e) {
                $this->_collectHelper->logError($e->getMessage());
            }
        } else {
            $quoteItems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
            foreach ($quoteItems as $quoteItem) {
                $result[] = $quoteItem->getSku();
            }
        }

        if (empty($result)) {
            throw new \Exception('COLLECT PLACE ==> Cannot get product SKU.');
        }

        return $result;
    }
}
