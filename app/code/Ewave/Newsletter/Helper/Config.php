<?php

namespace Ewave\Newsletter\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Ewave\Newsletter\Helper
 */
class Config extends AbstractHelper
{
    const XML_ENABLE_SUBSCRIBE_ON_CHECKOUT = 'newsletter/subscription/enable_subscribe_on_checkout';
    const XML_SUBSCRIBE_ON_CHECKOUT_TEXT = 'newsletter/subscription/subscribe_on_checkout_text';
    const XML_STOREFRONT_FIELDS = 'newsletter/subscription/storefront_fields';
    const XML_USE_BILLING_CUSTOMER_INFO_FOR_GUEST = 'newsletter/subscription/use_billing_info_for_guest';
    const XML_ENABLE_SUCCESS_EMAIL = 'newsletter/subscription/enable_success_email';
    const XML_ENABLE_UNSUBSCRIPTION_EMAIL = 'newsletter/subscription/enable_unsubscribtion_email';
    const XML_ENABLE_PAYPAL_SUBSCRIPTION = 'newsletter/subscription/enable_subscribe_on_paypal_review_page';
    const XML_SUBSCRIBERS_GRID_ADDITIONAL_CUSTOMER_ATTRIBUTES
        = 'newsletter/subscription/subscribers_grid_additional_customer_attributes';

    /**
     * Check if newsletter subscribe on checkout is enabled
     *
     * @param null $scopeCode
     * @return bool
     */
    public function isNewsletterSubscribeOnCheckoutEnabled($scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_ENABLE_SUBSCRIBE_ON_CHECKOUT,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Get newsletter subscribe checkbox text
     *
     * @param null $scopeCode
     * @return string
     */
    public function getNewsletterSubscribeOnCheckoutText($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_SUBSCRIBE_ON_CHECKOUT_TEXT,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Check if guest subscribe is allowed
     *
     * @param null $scopeCode
     * @return bool
     */
    public function isAllowGuestSubscribe($scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(
            \Magento\Newsletter\Model\Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Get selected store front fields
     *
     * @param null $scopeCode
     * @return array
     */
    public function getStorefrontFields($scopeCode = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_STOREFRONT_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );

        return explode(',', $value);
    }

    /**
     * Get use billing customer info for guest user
     *
     * @param null $scopeCode
     * @return bool
     */
    public function isUserBillingCustomerInfoForGuest($scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_USE_BILLING_CUSTOMER_INFO_FOR_GUEST,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * @param null $scopeCode
     * @return bool
     */
    public function isEnableSuccessEmail($scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_ENABLE_SUCCESS_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * @param null $scopeCode
     * @return bool
     */
    public function isEnableUnsubscriptionEmail($scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_ENABLE_UNSUBSCRIPTION_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Get info for guest user about is enable subscribe on paypal review page
     *
     * @param null $scopeCode
     * @return bool
     */
    public function isEnableSubscribeOnPaypal($scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_ENABLE_PAYPAL_SUBSCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Get additional customer attributes to display on newsletter subscribers grid
     *
     * @return array
     */
    public function getSubscribersGridAdditionalCustomerAttributes()
    {
        $value = $this->scopeConfig->getValue(self::XML_SUBSCRIBERS_GRID_ADDITIONAL_CUSTOMER_ATTRIBUTES);

        return explode(',', $value);
    }
}
