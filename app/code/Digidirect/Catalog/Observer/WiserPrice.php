<?php

namespace Digidirect\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class WiserPrice implements ObserverInterface
{
    protected $customer;
    
    protected $logger;
    
    protected $_productOptions;
    
    protected $_productRepositoryInterface;
    
    protected $_productRepository;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Product\Option $productOptions,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Catalog\Model\Product $productRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->customer = $customerSession;
        $this->_productOptions = $productOptions;
        $this->_productRepositoryInterface = $productRepositoryInterface;
        $this->_productRepository = $productRepository;
        $this->logger = $logger;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer) {
        
        //get the item just added to cart
        $item = $observer->getEvent()->getData('quote_item');
        $product = $observer->getEvent()->getData('product');
        $sku = $product->getData('sku');
        
        $discount2 = [122428,124928,130029,133828,135538,135790,137132,137431,139609,139908,142338,144625,144626,146718,146719,146818,146876,147859,148028,148817,149367,149381,152803,153589,153590,154948,154953,155161,155162,155166,155212,155520,155973];
        $discount5 = [137376,137952,139517,141539,142768,149131,149132,149133,149950,153379,153380,153381,155158,155159,156474,156475,156476,156477,117561,117562,134146,117492,117494,122574,149496,149497];
        $discount10 = [154979,154784,154782,154783,154785,151173,151169,141197,136339,148815,148728,155243,155244];
        $discount15 = [133160,147857,130998,117586,153643,153642];
        
        $isDigiClub = 0;
        
        if ($this->customer->isLoggedIn()) {
            $customerGroupId = $this->customer->getCustomer()->getGroupId();
            if ($customerGroupId == 10) {
                $isDigiClub = 1;
            }
        }
        
        //(optional) get the parent item, if exists
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        
        $price = $product->getData('final_price');
        $wiserPrice = $product->getData('wiser_price');
        $basePrice = $product->getPrice();
            
        $this->_productRepositoryInterface->getById($product->getId());
        $this->_productRepository->load($product->getId());

        $finalPrice = $price;
        
        $finalProductPrice = $finalPrice;
        
        $this->logger->info('$basePrice: ' . $basePrice . ', $finalPrice: ' . $finalPrice .', $wiserPrice: ' . $wiserPrice);
        
        if ($wiserPrice == 0 || empty($wiserPrice)) {
            $finalProductPrice = $finalPrice;
        } else {
            if ($wiserPrice > 1 && !empty($wiserPrice)) {
                if ($wiserPrice < $price) {
                    if ((in_array($sku, $discount2)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.02);
                    } elseif ((in_array($sku, $discount5)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.05);
                    } elseif ((in_array($sku, $discount10)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.10);
                    } elseif ((in_array($sku, $discount15)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.15);
                    } 
                    $finalPrice = $wiserPrice;
                } else {
                    $finalPrice = $price;
                }
            } else {
                $finalPrice = $price;
            }

            $digiProtectPrice = 0;

            $selectedOption = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            $this->logger->info('$selectedOption: ' . json_encode($selectedOption));

            $customOptions = $this->_productOptions->getProductOptionCollection($product);
            foreach($customOptions as $optionKey => $optionVal) {
                foreach($optionVal->getValues() as $valuesKey => $valuesVal) {
                    $this->logger->info('$valuesVal: ' . $valuesVal->getTitle(). ' ' .$valuesVal->getPrice());
                    if (isset($selectedOption['options'])) {
                        $digiProtectPrice = $valuesVal->getPrice();
                    }
                }
            }

            $this->logger->info('$finalPrice: ' . $finalPrice);
            $this->logger->info('$digiProtectPrice: ' . $digiProtectPrice);

            $finalProductPrice = $finalPrice + $digiProtectPrice;
        }
        
        $item->setCustomPrice($finalProductPrice);
        $item->setOriginalCustomPrice($finalProductPrice);
        $item->getProduct()->setIsSuperMode(true);
    }
}