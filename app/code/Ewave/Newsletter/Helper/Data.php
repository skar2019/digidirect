<?php

namespace Ewave\Newsletter\Helper;

use Ewave\Newsletter\Api\Data\SubscriberInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Ewave\Newsletter\Api\SubscriberRepositoryInterface;

/**
 * Class Config
 *
 * @package Ewave\Newsletter\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Ewave\Newsletter\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var SubscriberRepositoryInterface
     */
    protected $subscriberRepository;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param Config $configHelper
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param SubscriberRepositoryInterface $subscriberRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        Config $configHelper,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        SubscriberRepositoryInterface $subscriberRepository
    ) {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->accountManagement = $accountManagement;
        $this->configHelper = $configHelper;
        $this->customerUrl = $customerUrl;
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriberRepository = $subscriberRepository;
    }

    /**
     * Get config helper
     *
     * @return \Ewave\Newsletter\Helper\Config
     */
    public function getConfigHelper()
    {
        return $this->configHelper;
    }

    /**
     * Validates that if the current user is a guest, that they can subscribe to a newsletter.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function validateGuestSubscription()
    {
        if (!$this->configHelper->isAllowGuestSubscribe() && !$this->customerSession->isLoggedIn()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Sorry, but the administrator denied subscription for guests. Please <a href="%1">register</a>.',
                    $this->customerUrl->getRegisterUrl()
                )
            );
        }
    }

    /**
     * Validates the format of the email address
     *
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function validateEmailFormat($email)
    {
        if (!\Zend_Validate::is($email, 'EmailAddress')) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a valid email address.'));
        }
    }

    /**
     * Subscribe
     *
     * @param string $email
     * @return int
     */
    public function subscribe($email)
    {
        $this->validateEmailFormat($email);
        $this->validateGuestSubscription();
        $subscriber = $this->subscriberFactory->create();

        if (!$this->customerSession->isLoggedIn() && $this->configHelper->isUserBillingCustomerInfoForGuest()) {
            $subscriber->setCheckoutSubscribe(true);
        }

        return $subscriber->subscribe($email);
    }

    /**
     * Check is subscribe checkbox selected
     *
     * @param \Magento\Quote\Api\Data\PaymentInterface $method
     * @return bool
     */
    public function isSubscribeCheckboxSelected(\Magento\Quote\Api\Data\PaymentInterface $method)
    {
        $extAttributes = $method->getExtensionAttributes();
        if ($extAttributes && $extAttributes->getNewsletterIds()) {
            return true;
        }
        return false;
    }

    /**
     * Check is customer subscribed
     *
     * @return bool
     */
    public function isCustomerSubscribed()
    {
        return $this->customerSession->isLoggedIn()
            && $this->subscriberFactory->create()
                ->loadByCustomerId($this->customerSession->getCustomerId())
                ->isSubscribed();
    }

    /**
     * @return bool
     */
    public function isFirstNameEnabled()
    {
        return $this->isStoreForntFieldEnabled($this->getFirstnameFieldName());
    }

    /**
     * @return bool
     */
    public function isLastNameEnabled()
    {
        return $this->isStoreForntFieldEnabled($this->getLastnameFieldName());
    }

    /**
     * @return string
     */
    public function getFirstnameFieldName()
    {
        return SubscriberInterface::FIRSTNAME;
    }

    /**
     * @return string
     */
    public function getLastnameFieldName()
    {
        return SubscriberInterface::LASTNAME;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function isFieldSelected($field)
    {
        $storefrontFields = $this->getConfigHelper()->getStorefrontFields();
        return in_array($field, $storefrontFields);
    }

    /**
     * @param $field
     * @return bool
     */
    public function isStoreForntFieldEnabled($field)
    {
        return $this->isFieldSelected($field);
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param array $dataToSave
     * @return void
     */
    public function addStorefrontFields(\Magento\Newsletter\Model\Subscriber $subscriber, array $dataToSave)
    {
        if ($subscriber->getSubscriberId() && !empty($dataToSave)) {
            $storeFrontsubscriber = $this->subscriberRepository->getBySubscriberId($subscriber->getSubscriberId());
            if (!$storeFrontsubscriber->getSubscriberId()) {
                $storeFrontsubscriber->setSubscriberId($subscriber->getSubscriberId());
            }
            $storeFrontsubscriber->addData($dataToSave);
            $this->subscriberRepository->save($storeFrontsubscriber);
        }
    }
}
