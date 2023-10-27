<?php
namespace Digidirect\SellerShipping\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Quote\Model\Quote;

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

    protected $taxHelper;

    /**
     * @param \Digidirect\SellerShipping\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Digidirect\SellerShipping\Helper\Data $dataHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger,
        \Digidirect\SellerShipping\Helper\Tax $helperTax

    )
    {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->taxHelper = $helperTax;
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
        if ($this->taxHelper->isTaxEnabled() && $this->taxHelper->displayInclTax()) {
            $address = $this->_getAddressFromQuote($quote);
            $SellerShippingConfig['custom_fee_amount'] = $this->dataHelper->getSellerShipping() + $address->getFeeTax();
        }
        if ($this->taxHelper->isTaxEnabled() && $this->taxHelper->displayBothTax()) {

            $address = $this->_getAddressFromQuote($quote);
            $SellerShippingConfig['custom_fee_amount'] = $this->dataHelper->getSellerShipping();
            $SellerShippingConfig['custom_fee_amount_inc'] = $this->dataHelper->getSellerShipping() + $address->getFeeTax();

        }
        $SellerShippingConfig['displayInclTax'] = $this->taxHelper->displayInclTax();
        $SellerShippingConfig['displayExclTax'] = $this->taxHelper->displayExclTax();
        $SellerShippingConfig['displayBoth'] = $this->taxHelper->displayBothTax();
        $SellerShippingConfig['exclTaxPostfix'] = __('Excl. Tax');
        $SellerShippingConfig['inclTaxPostfix'] = __('Incl. Tax');
        $SellerShippingConfig['TaxEnabled'] = $this->taxHelper->isTaxEnabled();
        $SellerShippingConfig['show_hide_SellerShipping_block'] = ($enabled && ($minimumOrderAmount <= $subtotal) && $quote->getFee()) ? true : false;
        $SellerShippingConfig['show_hide_SellerShipping_shipblock'] = ($enabled && ($minimumOrderAmount <= $subtotal)) ? true : false;
        return $SellerShippingConfig;
    }

    protected function _getAddressFromQuote(Quote $quote)
    {
        return $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
    }
}
