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
        
        $discount2 = [136339,142517,139006,126737,148816,145953,132705,145951,129240,119068,138764,142809,128864,137428,131612,131613,142352,142348,142041,146748,154958,154957,138160,149051,151788,151197];
        $discount3 = [134649,146488,150391,150390,144586,141788,153391,149253,149252,149251,149250];
        $discount4 = [153686,133938,136565,142693,150685,139639,144624,142338,142339,135790,138974,154961,152228];
        $discount5 = [150826,150828,148151,146084,148152,137426,133439,133437,146744,146743,139972,140077,121374,138958,154550,116572,121880,155685,146876,145851,139373,138714,137478,139584,154979];
        $discount10 = [144700,118377,137132,149131,144893,138715,148217,154989,154991,139908,139909,148118,148121,146790,156332];
        $discount15 = [151302];
        $discount20 = [152693];
        $discount30 = [151857,151856];
        $discount50 = [148264];
        
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
            
        $this->_productRepositoryInterface->getById($product->getId());
        $this->_productRepository->load($product->getId());

        if (!$product->getData('added_by_rule_id')) {
            if ($wiserPrice > 1 && !empty($wiserPrice)) {
                if ($wiserPrice < $price) {
                    if ((in_array($sku, $discount2)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.02);
                    } elseif ((in_array($sku, $discount3)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.03);
                    } elseif ((in_array($sku, $discount4)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.04);
                    } elseif ((in_array($sku, $discount5)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.05);
                    } elseif ((in_array($sku, $discount10)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.10);
                    } elseif ((in_array($sku, $discount15)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.15);
                    } elseif ((in_array($sku, $discount20)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.20);
                    } elseif ((in_array($sku, $discount30)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.30);
                    } elseif ((in_array($sku, $discount50)) && $isDigiClub) {
                        $wiserPrice = $wiserPrice - ($wiserPrice * 0.50);
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
            
            $item->setCustomPrice($finalPrice);
            $item->setOriginalCustomPrice($finalPrice);
            $item->getProduct()->setIsSuperMode(true);
        }

    }
}