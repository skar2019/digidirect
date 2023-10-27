<?php

namespace Digidirect\SellerShipping\Model\Plugin\Checkout;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class DefaultConfigProvider
{
    
    protected $helperData;
    
    
    public function __construct(
        \Digidirect\SellerShipping\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * AfterGetConfig
     *
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param [] $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        $result
    ) {
        
        if ($this->helperData->getSellerShipping()) {
            $result['quoteData']['has_marketplacer_seller'] = true;
            $result['quoteData']['marketplacer_sellers'] = $this->helperData->getSellers();
        } else {
            $result['quoteData']['has_marketplacer_seller'] = false;
        }
        
        return $result;
    }

}
