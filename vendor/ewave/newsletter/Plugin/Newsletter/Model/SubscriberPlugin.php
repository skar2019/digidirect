<?php

namespace Ewave\Newsletter\Plugin\Newsletter\Model;

use Ewave\Newsletter\Api\Data\SubscriberInterface;
use Ewave\Newsletter\Helper\Data;
use Magento\Framework\App\RequestInterface;
use \Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Newsletter\Model\Subscriber;

/**
 * Class SubscriberPlugin
 * @package Ewave\Newsletter\Plugin\Newsletter\Model
 */
class SubscriberPlugin
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $dataToSave = [];

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * SubscriberPlugin constructor.
     * @param Data $helper
     * @param RequestInterface $request
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Data $helper,
        RequestInterface $request,
        CheckoutSession $checkoutSession,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subject
     * @param string $email
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSubscribe(Subscriber $subject, $email)
    {
        $enabledFields = $this->helper->getConfigHelper()->getStorefrontFields();
        foreach ($enabledFields as $field) {
            $value = $this->request->getPost($field);
            if ($this->helper->isStoreForntFieldEnabled($field)) {
                if ($value !== null) {
                    if (empty($value)) {
                        throw new LocalizedException(__('%1 is required field.', $field));
                    }
                    $this->dataToSave[$field] = $value;
                }
            }
        }

        if (empty($this->dataToSave) && $subject->getCheckoutSubscribe()) {
            $billingAddress = $this->checkoutSession->getQuote()->getBillingAddress();
            $this->dataToSave = [
                SubscriberInterface::FIRSTNAME => $billingAddress->getFirstname(),
                SubscriberInterface::LASTNAME => $billingAddress->getLastname()
            ];
        }

        return [$email];
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subject
     * @return void
     */
    public function afterSubscribe(Subscriber $subject)
    {
        try {
            $this->helper->addStorefrontFields($subject, $this->dataToSave);
        } catch (\Exception $e) {
            $this->logger->error(
                __('There was a problem with the store front fields subscription: %1', $e->getMessage())
            );
            throw new LocalizedException(__('There was a problem with subscription'));
        }
    }

    /**
     * @param Subscriber $subject
     * @param callable $proceed
     */
    public function aroundSendConfirmationSuccessEmail(Subscriber $subject, callable $proceed)
    {
        if ($this->helper->getConfigHelper()->isEnableSuccessEmail()) {
            $proceed();
        }
    }

    /**
     * @param Subscriber $subject
     * @param callable $proceed
     */
    public function aroundSendUnsubscriptionEmail(Subscriber $subject, callable $proceed)
    {
        if ($this->helper->getConfigHelper()->isEnableUnsubscriptionEmail()) {
            $proceed();
        }
    }
}
