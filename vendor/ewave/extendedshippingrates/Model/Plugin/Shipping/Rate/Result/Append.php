<?php
namespace Ewave\ExtendedShippingRates\Model\Plugin\Shipping\Rate\Result;

use Ewave\ExtendedShippingRates\Model\ValidatorsAggregator;
use Ewave\ExtendedShippingRates\Model\RuleAppliersAggregator;

/**
 * Class Append
 * @package Ewave\ExtendedShippingRates\Model\Plugin\Shipping\Rate\Result
 */
class Append
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
     * Append constructor.
     * @param ValidatorsAggregator $validatorsAggregator
     * @param RuleAppliersAggregator $ruleAppliersAggregator
     */
    public function __construct(
        ValidatorsAggregator $validatorsAggregator,
        RuleAppliersAggregator $ruleAppliersAggregator
    ) {
        $this->validatorsAggregator = $validatorsAggregator;
        $this->ruleAppliersAggregator = $ruleAppliersAggregator;
    }

    /**
     * Validate shipping methods before append.
     * @see \Ewave\ExtendedShippingRates\Observer\Sales\Quote\Address\CollectTotalsAfter
     * by checking the value of this mark in the rate object.
     *
     * NOTE: If you have some problems with the rules and the shipping methods, start debugging from here.
     *
     * @param \Magento\Shipping\Model\Rate\Result $subject
     * @param \Magento\Quote\Model\Quote\Address\RateResult\AbstractResult|\Magento\Shipping\Model\Rate\Result $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeAppend($subject, $result)
    {
        if (!$result instanceof \Magento\Quote\Model\Quote\Address\RateResult\Method) {
            return [$result];
        }

        $quote = $this->validatorsAggregator->getCurrentProcessedQuote();
        $validator = $this->validatorsAggregator->getValidatorByCurrentProcessedQuote();
        $rulesApplier = $this->ruleAppliersAggregator->getRulesApplierByCurrentProcessedQuote();

        $storeId = $quote->getStore()->getStoreId();
        $customerGroup = $quote->getCustomerGroupId();

        $validator->init($storeId, $customerGroup);
        if ($validator->validate($result)) {
            $rules = $validator->getAvailableRulesForRate($result);
            $conditionalRules = $validator->getConditionallyAvailableRulesForRate($result);
            $result = $rulesApplier->applyRules($result, $rules, $conditionalRules);
        }

        return [$result];
    }
}
