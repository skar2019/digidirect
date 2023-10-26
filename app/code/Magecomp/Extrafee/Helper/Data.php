<?php

namespace Magecomp\Extrafee\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * Custom fee config path
     */
    const CONFIG_CUSTOM_IS_ENABLED = 'Extrafee/Extrafee/status';
    const CONFIG_CUSTOM_FEE = 'Extrafee/Extrafee/Extrafee_amount';
    const CONFIG_FEE_LABEL = 'Extrafee/Extrafee/name';
    const CONFIG_MINIMUM_ORDER_AMOUNT = 'Extrafee/Extrafee/minimum_order_amount';
    
    protected $cart;
    
    protected $logger;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->cart = $cart;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function isModuleEnabled()
    {
        //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('Extrafee/Extrafee/status', 'store');
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getExtrafee()
    {
        $items = $this->cart->getQuote()->getAllItems();
        foreach($items as $item) {
            $this->logger->info('getData: ' . $item->getData('marketplacer_seller'));
            $this->logger->info('getMarketplacerSeller: ' . $item->getMarketplacerSeller());
        }
        
        return 15;
        
        //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        //return $this->scopeConfig->getValue('Extrafee/Extrafee/Extrafee_amount', $storeScope);
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getFeeLabel()
    {
        //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('Extrafee/Extrafee/name', 'store');
    }

    /**
     * @return mixed
     */
    public function getMinimumOrderAmount()
    {
        //$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('Extrafee/Extrafee/minimum_order_amount', 'store');
    }
}
