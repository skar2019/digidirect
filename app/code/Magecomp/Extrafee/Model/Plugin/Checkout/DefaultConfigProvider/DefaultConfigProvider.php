<?php

namespace Digidirect\Collect\Model\Plugin\Checkout;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class DefaultConfigProvider
{
    
    protected $helperData;
    
    
    public function __construct(
        \Magecomp\Extrafee\Helper\Data $helperData
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
        
        if ($this->helperData->getExtrafee()) {
            $result['quoteData']['has_marketplacer_seller'] = true;
        } else {
            $result['quoteData']['has_marketplacer_seller'] = false;
        }
        
        return $result;
    }

}
