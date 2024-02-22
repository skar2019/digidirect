<?php

namespace Digidirect\SellerShipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Checkout\Model\Cart;

class Data extends AbstractHelper
{
    
    protected $session;
    
    protected $logger;
    
    protected $productFactory;
    
    protected $cart;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $session,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        Cart $cart
    ){
        $this->session = $session;
        $this->logger = $logger;
        $this->productFactory = $productFactory;
        $this->cart = $cart;
        parent::__construct($context);
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getSellerShipping()
    {
        //$items = $this->session->getQuote()->getAllVisibleItems();
        $items = $this->cart->getQuote()->getAllItems();
        $sellers = [];
        foreach($items as $item) {
            //$this->logger->info('getProductId: ' . $item->getProductId());
            $product = $this->productFactory->create()->load($item->getProductId());
            //$this->logger->info('getAttributeText: ' . $product->getAttributeText('marketplacer_seller'));
            //$this->logger->info('getData: ' . $product->getData('marketplacer_seller'));
            //$this->logger->info('getMarketplacerSeller: ' . $product->getMarketplacerSeller());
            //$this->logger->info('getSku: ' . $product->getSku());
            //$this->logger->info('getName: ' . $product->getName());
            //$this->logger->info('getFinalPrice: ' . $product->getFinalPrice());
            
            $seller = $product->getAttributeText('marketplacer_seller');
            
            if ($seller == "") {
                $seller = "digiDirect";
            }
            
            if (($seller != "digiDirect") && (!in_array($seller, $sellers)))  {
                array_push($sellers, $seller);
            }
            //$this->logger->info('getProductId: ' . $product->getId());
        }
        
        $sellerTotalShipping = 0;
        
        $digidirectSeller = 0;
        
        $nonDigidirectSeller = 0;
        
        $nonDigidirectSellerCount = 0;
        
        $standardShipping = 8.95;
        
        foreach($sellers as $seller) {
            $sellerShipping = 0;
            $sellerTotal = 0;
            foreach($items as $item) {
                $product = $this->productFactory->create()->load($item->getProductId());
                //$this->logger->info('getFinalPrice: ' . $product->getFinalPrice());
                $finalPrice = $product->getFinalPrice();
                $productTotal = $finalPrice * $item->getQty();
                $itemSeller = $product->getAttributeText('marketplacer_seller');
            
                if ($seller == $itemSeller) {
                    $sellerTotal += $productTotal;
                }
            }
            //$this->logger->info('getSellerShipping: ' . $seller . ',' . $sellerTotal);
            
            
            if ($seller != "digiDirect") {
                $nonDigidirectSeller += $standardShipping;
                $nonDigidirectSellerCount++;
            }
        }
        
        /*if ($nonDigidirectSellerCount > 1) {
            $nonDigidirectSeller = $nonDigidirectSeller - $standardShipping;
        }*/
        
        $this->logger->info('$nonDigidirectSellerCount: ' . $nonDigidirectSellerCount);
        $this->logger->info('$digidirectSeller: ' . $digidirectSeller);
        $this->logger->info('$nonDigidirectSeller: ' . $nonDigidirectSeller);
        
        $sellerTotalShipping = $nonDigidirectSeller;
        //$sellerCount = count($sellers);
        //$sellerTotalShipping = $sellerCount * $baseShipping;
        //$this->logger->info('sellerTotalShipping: ' . $sellerTotalShipping);
        //$finalSellerTotalShipping = $sellerTotalShipping - $standardShipping;
        
        return $sellerTotalShipping;
        
    }
    
    public function getSellers()
    {
        $items = $this->cart->getQuote()->getAllItems();
        $sellers = [];
        foreach($items as $item) {
            $product = $this->productFactory->create()->load($item->getProductId());
            $seller = $product->getAttributeText('marketplacer_seller');
            
            if ($seller == "") {
                $seller = "digiDirect";
            }
            
            if (!in_array($seller, $sellers))  {
                array_push($sellers, $seller);
            }
        }
        
        $sellerTotalShipping = 0;
        $sellersArray = [];
        
        foreach($sellers as $seller) {
            
            $sellerShipping = 0;
            $sellerTotal = 0;
            
            foreach($items as $item) {
                $product = $this->productFactory->create()->load($item->getProductId());
                $finalPrice = $product->getFinalPrice();
                $productTotal = $finalPrice * $item->getQty();
                $itemSeller = $product->getAttributeText('marketplacer_seller');
                
                if ($seller == $itemSeller) {
                    $sellerTotal += $productTotal;
                }
            }
            
            /*if ($sellerTotal < 99) {
                if ($seller == "digiDirect") {
                    $sellerShipping = 8.95;
                } else {
                    $sellerShipping = 8.95;
                }
            }*/
            $sellerShipping = 8.95;
            
            if (!in_array($seller, $sellersArray))  {
                array_push($sellersArray, [$seller,$sellerShipping]);
            }
        }
        
        //$this->logger->info('sellersArray: ' . json_encode($sellersArray));
        
        return $sellersArray;
        
    }
    
    public function hasMarketplacerSeller() {
        $sellers = $this->getSellers();
        $thirdPartyCount = 0;
        
        foreach($sellers as $seller){
            if ($seller[0] != "digiDirect") {
                $thirdPartyCount++;
            }
        }
        
        if ($thirdPartyCount > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    
    public function getDigiShipping()
    {
        /*$items = $this->cart->getQuote()->getAllItems();
        
        $digiShipping = 0;
        
        $digiTotal = 0;
        
        foreach($items as $item) {
            $product = $this->productFactory->create()->load($item->getProductId());
            //$this->logger->info('getFinalPrice: ' . $product->getFinalPrice());
            $finalPrice = $product->getFinalPrice();
            $productTotal = $finalPrice * $item->getQty();
            $itemSeller = $product->getAttributeText('marketplacer_seller');
            
            if ($itemSeller == "") {
                $itemSeller = "digiDirect";
            }

            if ($itemSeller == "digiDirect") {
                $digiTotal += $productTotal;
            }
        }
        //$this->logger->info('getDigiShipping: ' . $digiTotal);

        if (($digiTotal < 99) && ($digiTotal != 0)) {
            $digiShipping = 8.95;
        }

        //$this->logger->info('digiShipping : ' . $digiShipping);
        
        return $digiShipping;*/
        return 8.95;
    }
}
