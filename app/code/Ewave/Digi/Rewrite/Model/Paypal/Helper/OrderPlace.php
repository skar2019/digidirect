<?php

namespace Ewave\Digi\Rewrite\Model\Paypal\Helper;

use Magento\Braintree\Model\Paypal\Helper\OrderPlace as PayPalOrderPlace;
use Magento\Quote\Model\Quote;
use Magento\Braintree\Model\Paypal\OrderCancellationService;
use Magento\Checkout\Api\AgreementsValidatorInterface;
use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OrderPlace
 * @package Ewave\Digi\Rewrite\Model\Paypal\Helper
 */
class OrderPlace extends PayPalOrderPlace
{
    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var AgreementsValidatorInterface
     */
    private $agreementsValidator;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Data
     */
    private $checkoutHelper;

    /**
     * @var OrderCancellationService
     */
    private $orderCancellationService;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CartManagementInterface $cartManagement
     * @param AgreementsValidatorInterface $agreementsValidator
     * @param Session $customerSession
     * @param Data $checkoutHelper
     * @param OrderCancellationService $orderCancellationService
     * @param LoggerInterface $logger
     */
    public function __construct(
        CartManagementInterface $cartManagement,
        AgreementsValidatorInterface $agreementsValidator,
        Session $customerSession,
        Data $checkoutHelper,
        OrderCancellationService $orderCancellationService,
        LoggerInterface $logger
    ) {
        $this->cartManagement = $cartManagement;
        $this->agreementsValidator = $agreementsValidator;
        $this->customerSession = $customerSession;
        $this->checkoutHelper = $checkoutHelper;
        $this->orderCancellationService = $orderCancellationService;
        $this->logger = $logger;
    }

    /**
     * Execute operation
     *
     * @param Quote $quote
     * @param array $agreement
     * @return void
     * @throws \Exception
     */
    public function execute(Quote $quote, array $agreement)
    {
        $this->logger->info(
            '#307048 trace execute method during place order',
            [
                'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                'argument' => $agreement,
                'quoteId' => $quote->getId()
            ]
        );
        if (!$this->agreementsValidator->isValid($agreement)) {
            throw new LocalizedException(__(
                "The order wasn't placed. First, agree to the terms and conditions, then try placing your order again."
            ));
        }

        if ($this->getCheckoutMethod($quote) === Onepage::METHOD_GUEST) {
            $this->prepareGuestQuote($quote);
        }

        $this->disabledQuoteAddressValidation($quote);

        $quote->collectTotals();
        try {
            $this->cartManagement->placeOrder($quote->getId());
        } catch (\Exception $e) {
            $this->logger->critical('#307048 message ' . $e->getMessage(), ['trace' => $e->getTrace()]);
            if ($quote->getReservedOrderId()) {
                $this->orderCancellationService->execute($quote->getReservedOrderId());
            }
            throw $e;
        }
    }

    /**
     * Get checkout method
     *
     * @param Quote $quote
     * @return string
     */
    private function getCheckoutMethod(Quote $quote)
    {
        if ($this->customerSession->isLoggedIn()) {
            return Onepage::METHOD_CUSTOMER;
        }
        if (!$quote->getCheckoutMethod()) {
            if ($this->checkoutHelper->isAllowedGuestCheckout($quote)) {
                $quote->setCheckoutMethod(Onepage::METHOD_GUEST);
            } else {
                $quote->setCheckoutMethod(Onepage::METHOD_REGISTER);
            }
        }

        return $quote->getCheckoutMethod();
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @param Quote $quote
     * @return void
     */
    private function prepareGuestQuote(Quote $quote)
    {
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Group::NOT_LOGGED_IN_ID);
    }
}