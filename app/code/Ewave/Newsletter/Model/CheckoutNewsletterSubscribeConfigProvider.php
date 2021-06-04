<?php

namespace Ewave\Newsletter\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class CheckoutNewsletterSubscribeConfigProvider
 *
 * @package Ewave\Newsletter\Model
 */
class CheckoutNewsletterSubscribeConfigProvider implements ConfigProviderInterface
{
    const CHECKOUT_NEWSLETTERS = 'checkoutNewsletterSubscribe';

    const PARAM_IS_ENABLED = 'isEnabled';
    const PARAM_NEWSLETTERS = 'newsletters';
    const PARAM_NEWSLETTER_ID = 'newsletterId';
    const PARAM_IS_SUBSCRIBED = 'isSubscribed';
    const PARAM_CHECKBOX_TEXT = 'checkboxText';

    const NEWSLETTER_ID = 1;

    /**
     * @var \Ewave\Newsletter\Helper\Data
     */
    protected $helper;

    /**
     * CheckoutNewsletterSubscribeConfigProvider constructor.
     *
     * @param \Ewave\Newsletter\Helper\Data $helper
     */
    public function __construct(
        \Ewave\Newsletter\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $subscribe = [];
        $subscribe[self::CHECKOUT_NEWSLETTERS] = [
            self::PARAM_IS_ENABLED => $this->isEnabled(),
            self::PARAM_NEWSLETTERS => $this->getNewsletters()
        ];
        return $subscribe;
    }

    /**
     * Is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helper->getConfigHelper()->isNewsletterSubscribeOnCheckoutEnabled();
    }

    /**
     * Get newsletters
     *
     * @return array
     */
    public function getNewsletters()
    {
        return [
            [
                self::PARAM_NEWSLETTER_ID => self::NEWSLETTER_ID,
                self::PARAM_CHECKBOX_TEXT => $this->helper->getConfigHelper()->getNewsletterSubscribeOnCheckoutText(),
                self::PARAM_IS_SUBSCRIBED => $this->helper->isCustomerSubscribed(),
            ],
        ];
    }
}
