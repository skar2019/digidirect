<?php

namespace Ewave\ExtendedShippingRatesShippingAvailability\Plugin\ShippingAvailabilityCheck\Model;

use Ewave\ExtendedShippingRates\Model\RuleAppliersAggregator;
use Ewave\ExtendedShippingRates\Model\ValidatorsAggregator;
use Ewave\ExtendedShippingRatesShippingAvailability\Model\AddressPreparer;

/**
 * Class CheckManagement
 * @package Ewave\ExtendedShippingRatesShippingAvailability\Plugin\ShippingAvailabilityCheck\Model
 */
class CheckManagement
{
    /**
     * @var ValidatorsAggregator
     */
    protected $validatorsAggregator;

    /**
     * @var RuleAppliersAggregator
     */
    protected $ruleAppliersAggregator;

    /**
     * @var AddressPreparer
     */
    protected $addressPreparer;

    /**
     * CheckManagement constructor.
     *
     * @param AddressPreparer $addressPreparer
     * @param ValidatorsAggregator $validatorsAggregator
     * @param RuleAppliersAggregator $ruleAppliersAggregator
     */
    public function __construct(
        AddressPreparer $addressPreparer,
        ValidatorsAggregator $validatorsAggregator,
        RuleAppliersAggregator $ruleAppliersAggregator
    ) {
        $this->validatorsAggregator = $validatorsAggregator;
        $this->ruleAppliersAggregator = $ruleAppliersAggregator;
        $this->addressPreparer = $addressPreparer;
    }

    /**
     * @param \Ewave\ShippingAvailabilityCheck\Model\CheckManagement $subject
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeEstimateByAddress(
        \Ewave\ShippingAvailabilityCheck\Model\CheckManagement $subject,
        $quote,
        \Magento\Quote\Api\Data\EstimateAddressInterface $address
    ) {
        if ($address->getPostcode()) {
            $address = $this->addressPreparer->prepareAddressByZone($address);
        }
        $this->validatorsAggregator->setCurrentProcessedQuote($quote);
        $this->ruleAppliersAggregator->setCurrentProcessedQuote($quote);
        return [$quote, $address];
    }

    /**
     * @param \Ewave\ShippingAvailabilityCheck\Model\CheckManagement $subject
     * @param array|\Magento\Quote\Api\Data\ShippingMethodInterface[] $output
     * @return array|\Magento\Quote\Api\Data\ShippingMethodInterface[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterEstimateByAddress(\Ewave\ShippingAvailabilityCheck\Model\CheckManagement $subject, $output)
    {
        $this->validatorsAggregator->resetCurrentProcessedQuote();
        $this->ruleAppliersAggregator->resetCurrentProcessedQuote();
        return $output;
    }

    /**
     * @param \Ewave\ShippingAvailabilityCheck\Model\CheckManagement $subject
     * @param $carrierModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeGetAllowedMethods(
        \Ewave\ShippingAvailabilityCheck\Model\CheckManagement $subject,
        $carrierModel
    ) {
        if ($carrierModel instanceof \Ewave\ExtendedShippingRates\Model\Carrier\Artificial) {
            $carrierModel->setOnlyActive(true);
        }
    }
}
