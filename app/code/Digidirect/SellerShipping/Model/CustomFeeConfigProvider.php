<?php
namespace Digidirect\SellerShipping\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class SellerShippingConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Digidirect\SellerShipping\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Digidirect\SellerShipping\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Digidirect\SellerShipping\Helper\Data $dataHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger

    )
    {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $SellerShippingConfig = [];
        $enabled = $this->dataHelper->isModuleEnabled();
        $minimumOrderAmount = $this->dataHelper->getMinimumOrderAmount();
        $SellerShippingConfig['fee_label'] = $this->dataHelper->getFeeLabel();
        $quote = $this->checkoutSession->getQuote();
        $subtotal = $quote->getSubtotal();
        $SellerShippingConfig['custom_fee_amount'] = $this->dataHelper->getSellerShipping();
        $SellerShippingConfig['show_hide_SellerShipping_block'] = ($enabled && ($minimumOrderAmount <= $subtotal) && $quote->getFee()) ? true : false;
        $SellerShippingConfig['show_hide_SellerShipping_shipblock'] = ($enabled && ($minimumOrderAmount <= $subtotal)) ? true : false;
        return $SellerShippingConfig;
    }
}
