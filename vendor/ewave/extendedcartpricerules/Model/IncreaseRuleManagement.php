<?php

namespace Ewave\ExtendedCartPriceRules\Model;

use Ewave\ExtendedCartPriceRules\Model\Rule\Action\Discount\IncreaseItemPrice;
use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\QuoteRepository\SaveHandler;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as RulesCollection;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\SalesRule\Model\Validator;
use Magento\Store\Model\StoreManagerInterface;

class IncreaseRuleManagement
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var SaveHandler
     */
    protected $quoteSaveHandler;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * IncreaseRuleManagement constructor.
     * @param CartRepositoryInterface $quoteRepository
     * @param CollectionFactory $collectionFactory
     * @param SaveHandler $quoteSaveHandler
     * @param StoreManagerInterface $storeManager
     * @param Validator $validator
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        CollectionFactory $collectionFactory,
        SaveHandler $quoteSaveHandler,
        StoreManagerInterface $storeManager,
        Validator $validator
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->ruleCollectionFactory = $collectionFactory;
        $this->quoteSaveHandler = $quoteSaveHandler;
        $this->storeManager = $storeManager;
        $this->validator = $validator;
    }

    /**
     * @param int $cartId
     * @return Quote
     */
    public function processRule($cartId)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $quoteItems = $quote->getAllItems();
        if (!empty($quoteItems)) {
            if ($quote->getAppliedRuleIds()) {
                foreach ($quoteItems as $item) {
                    $this->addPriceByIncreaseAmountRule($quote->getAppliedRuleIds(), $item);
                }
            }
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->collectTotals();
            $this->quoteSaveHandler->save($quote);
        }
        return $quote;
    }

    /**
     * @param int $cartId
     * @param TotalsInformationInterface $addressInformation
     * @return void
     */
    public function processRuleByAddressInformation($cartId, TotalsInformationInterface $addressInformation)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        $quoteItems = $quote->getAllItems();
        if (!empty($quoteItems)) {
            if ($quote->getIsVirtual()) {
                $quote->setBillingAddress($addressInformation->getAddress());
            } else {
                $quote->setShippingAddress($addressInformation->getAddress());
                $quote->getShippingAddress()->setCollectShippingRates(true)->setShippingMethod(
                    $addressInformation->getShippingCarrierCode() . '_' . $addressInformation->getShippingMethodCode()
                );
            }
            /** @var Item $item */
            foreach ($quote->getAllItems() as $item) {
                $quote = $item->getQuote();
                $this->validator->init(
                    $this->storeManager->getWebsite()->getId(),
                    $quote->getCustomerGroupId(),
                    $quote->getCouponCode()
                );
                $this->validator->process($item);
                if (!empty($item->getAppliedRuleIds())) {
                    $this->addPriceByIncreaseAmountRule($item->getAppliedRuleIds(), $item);
                }
            }
        }
    }

    /**
     * @param string $ruleIds
     * @param AbstractItem $quoteItem
     * @return Item|AbstractItem
     */
    protected function addPriceByIncreaseAmountRule($ruleIds, AbstractItem $quoteItem)
    {
        if (!$quoteItem->getOriginalCustomPrice()) {
            $ruleIds = explode(',', $ruleIds);
            $increaseAmount = 0;
            $price = $quoteItem->getPrice();
            $rules = $this->loadRules($ruleIds);
            /** @var \Magento\SalesRule\Model\Rule $rule */
            foreach ($rules as $rule) {
                $increaseAmount += $rule->getDiscountAmount() * $price / 100;
            }
            if ($increaseAmount) {
                $price += $increaseAmount;
                $quoteItem = $quoteItem->getParentItem() ?? $quoteItem;
                $quoteItem->setOriginalCustomPrice($price);
            }
        }

        return $quoteItem;
    }

    /**
     * @param array $ruleIds
     * @return RulesCollection
     */
    protected function loadRules(array $ruleIds)
    {
        $ruleIds = array_diff($ruleIds, array_keys($this->rules));
        if (!empty($ruleIds)) {
            $rules = $this->ruleCollectionFactory->create();
            $rules->addFieldToFilter('simple_action', IncreaseItemPrice::SIMPLE_ACTION)
                ->addFieldToFilter('rule_id', ['in' => $ruleIds]);
            foreach ($rules as $rule) {
                if (!isset($this->rules[$rule->getRuleId()])) {
                    $this->rules[$rule->getRuleId()] = $rule;
                }
            }
        }
        return $this->rules;
    }
}
