<?php

namespace Ewave\ExtendedCartPriceRules\Model;

/**
 * Class RuleManagement
 *
 * @package Ewave\ExtendedCartPriceRules\Model
 */
class RuleManagement implements \Ewave\ExtendedCartPriceRules\Api\RuleManagementInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Ewave\ExtendedCartPriceRules\Helper\Data
     */
    protected $helper;

    /**
     * RuleManagement constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Ewave\ExtendedCartPriceRules\Helper\Data $helper
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Ewave\ExtendedCartPriceRules\Helper\Data $helper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->helper = $helper;
    }

    /**
     * @param null|int $cartId
     * @return array
     */
    public function getPaymentMethodLimit($cartId = null)
    {
        $quote = null;
        if ($cartId) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->get($cartId);
        }

        /**
         * @var array $output
         */
        $output = $this->helper->getAvailableMethods($quote);

        return $output;
    }
}
