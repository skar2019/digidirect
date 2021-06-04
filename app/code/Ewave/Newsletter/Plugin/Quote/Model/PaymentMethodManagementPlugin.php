<?php

namespace Ewave\Newsletter\Plugin\Quote\Model;

/**
 * Class PaymentMethodManagementPlugin
 *
 * @package Ewave\Newsletter\Plugin\Model\Quote
 */
class PaymentMethodManagementPlugin
{
    /**
     * @var \Ewave\Newsletter\Helper\Config
     */
    protected $helper;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * PaymentMethodManagementPlugin constructor.
     *
     * @param \Ewave\Newsletter\Helper\Data $helper
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Ewave\Newsletter\Helper\Data $helper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $subject
     * @param \Closure $closure
     * @param $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $method
     * @return mixed
     * @see \Magento\Quote\Model\PaymentMethodManagement::set()
     */
    public function aroundSet(
        \Magento\Quote\Api\PaymentMethodManagementInterface $subject,
        \Closure $closure,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $method
    ) {
        $result = $closure($cartId, $method);

        if ($this->helper->getConfigHelper()->isNewsletterSubscribeOnCheckoutEnabled()
            && $this->helper->isSubscribeCheckboxSelected($method)
        ) {
            try {
                /** @var \Magento\Quote\Model\Quote $quote */
                $quote = $this->quoteRepository->get($cartId);

                $email = $quote->getCustomerEmail() ?:
                    $this->customerSession->getCustomerDataObject()->getEmail() ?:
                        $quote->getBillingAddress()->getEmail();

                $this->helper->subscribe($email);
            } catch (\Exception $e) {
                $this->logger->error(__('There was a problem with the subscription: %1', $e->getMessage()));
            }
        }

        return $result;
    }
}
