<?php

namespace Ewave\Newsletter\Plugin\Paypal\Controller\Express;

/**
 * Class PlaceOrderPlugin
 *
 * @package Ewave\Newsletter\Plugin\Paypal\Controller\Express
 */
class PlaceOrderPlugin
{
    /**
     * @var \Ewave\Newsletter\Helper\Config
     */
    protected $helper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * PlaceOrderPlugin constructor.
     *
     * @param \Ewave\Newsletter\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Ewave\Newsletter\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Paypal\Controller\Express\PlaceOrder $subject
     * @return bool
     * @see \Magento\Paypal\Controller\Express\PlaceOrder::execute()
     */
    public function beforeExecute(\Magento\Paypal\Controller\Express\PlaceOrder $subject) {
        if ($this->request->getPost('subscribe_updates')) {
            try {
                $email = $this->getEmail();
                $this->helper->subscribe($email);
            } catch (\Exception $e) {
                $this->logger->error(__('There was a problem with the subscription: %1', $e->getMessage()));
            }
        }
        return true;
    }

    /**
     * Get email from customer session or from checkout session
     *
     * @return string
     */
    protected function getEmail()
    {
        $email = $this->checkoutSession->getQuote()->getCustomerEmail() ?:
            $this->customerSession->getCustomerDataObject()->getEmail() ?:
                $this->checkoutSession->getQuote()->getBillingAddress()->getEmail();
        return $email;
    }

}
