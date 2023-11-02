<?php

namespace Digidirect\SellerShipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * Custom fee config path
     */
    const CONFIG_CUSTOM_IS_ENABLED = 'SellerShipping/SellerShipping/status';
    const CONFIG_CUSTOM_FEE = 'SellerShipping/SellerShipping/SellerShipping_amount';
    const CONFIG_FEE_LABEL = 'SellerShipping/SellerShipping/name';
    const CONFIG_MINIMUM_ORDER_AMOUNT = 'SellerShipping/SellerShipping/minimum_order_amount';
    
    protected $session;
    
    protected $logger;
    
    protected $productFactory;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $session,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ){
        $this->session = $session;
        $this->logger = $logger;
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function isModuleEnabled()
    {
        //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('SellerShipping/SellerShipping/status', 'store');
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getSellerShipping()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $baseShipping = $this->scopeConfig->getValue('SellerShipping/SellerShipping/SellerShipping_amount', $storeScope);
        
        $items = $this->session->getQuote()->getAllVisibleItems();
        //$this->logger->info('getAllItems');
        $sellers = [];
        foreach($items as $item) {
            $this->logger->info('getProductId: ' . $item->getProductId());
            $product = $this->productFactory->create()->load($item->getProductId());
            $this->logger->info('getAttributeText: ' . $product->getAttributeText('marketplacer_seller'));
            $this->logger->info('getData: ' . $product->getData('marketplacer_seller'));
            $this->logger->info('getMarketplacerSeller: ' . $product->getMarketplacerSeller());
            $this->logger->info('getSku: ' . $product->getSku());
            $this->logger->info('getName: ' . $product->getName());
            $this->logger->info('getFinalPrice: ' . $product->getFinalPrice());
            
            $seller = $product->getAttributeText('marketplacer_seller');
            
            if (($seller != "General Seller") && (!in_array($seller, $sellers)))  {
                array_push($sellers, $seller);
            }
            
            //$this->logger->info('getProductId: ' . $product->getId());
        }
        
        $sellerTotalShipping = 0;
        
        foreach($sellers as $seller) {
            $sellerShipping = 0;
            $sellerTotal = 0;
            foreach($items as $item) {
                $product = $this->productFactory->create()->load($item->getProductId());
                $this->logger->info('getFinalPrice: ' . $product->getFinalPrice());
                $finalPrice = $product->getFinalPrice();
                $itemSeller = $product->getAttributeText('marketplacer_seller');
                
                if ($seller == $itemSeller) {
                    $sellerTotal += $finalPrice;
                }
            }
            $this->logger->info('getSellerShipping' . $seller . ': ' . $sellerTotal);
            
            if ($sellerTotal < 99) {
                $sellerShipping = 10;
            }
            
            $sellerTotalShipping += $sellerShipping;
            
            $this->logger->info($itemSeller . ': ' . $sellerTotalShipping);
        }
        
        //$sellerCount = count($sellers);
        //$sellerTotalShipping = $sellerCount * $baseShipping;
        $this->logger->info('sellerTotalShipping: ' . $sellerTotalShipping);
        return $sellerTotalShipping;
        
    }
    
    public function getSellers()
    {
        $items = $this->session->getQuote()->getAllVisibleItems();
        //$this->logger->info('getAllItems');
        $sellers = [];
        foreach($items as $item) {
            $product = $this->productFactory->create()->load($item->getProductId());
            $seller = $product->getAttributeText('marketplacer_seller');
            if (!in_array($seller, $sellers))  {
                array_push($sellers, $seller);
            }
            //$this->logger->info('getProductId: ' . $product->getId());
        }
        
        $sellerTotalShipping = 0;
        $sellersArray = [];
        
        foreach($sellers as $seller) {
            
            $sellerShipping = 0;
            $sellerTotal = 0;
            
            foreach($items as $item) {
                $product = $this->productFactory->create()->load($item->getProductId());
                $this->logger->info('getFinalPrice: ' . $product->getFinalPrice());
                $finalPrice = $product->getFinalPrice();
                $itemSeller = $product->getAttributeText('marketplacer_seller');
                
                if ($seller == $itemSeller) {
                    $sellerTotal += $finalPrice;
                }
            }
            $this->logger->info($seller . ': ' . $sellerTotal);
            
            if ($sellerTotal < 99) {
                $sellerShipping = 10;
            }
            
            //$sellerTotalShipping += $sellerShipping;
            
            if (!in_array($seller, $sellersArray))  {
                array_push($sellersArray, [$seller,$sellerShipping]);
            }
        }
        
        $this->logger->info('sellersArray: ' . json_encode($sellersArray));
        
        return $sellersArray;
        
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getFeeLabel()
    {
        //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('SellerShipping/SellerShipping/name', 'store');
    }

    /**
     * @return mixed
     */
    public function getMinimumOrderAmount()
    {
        //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('SellerShipping/SellerShipping/minimum_order_amount', 'store');
    }
}
