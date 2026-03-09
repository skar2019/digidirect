<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Newsletter
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\Newsletter\Controller\Subscriber;

use Magento\Framework\Exception\LocalizedException;
use Magento\Newsletter\Model\Subscriber;

class NewAction extends \Magento\Newsletter\Controller\Subscriber\NewAction
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement
     * @param \Magento\Newsletter\Model\SubscriptionManagerInterface $subscriptionManager
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Magento\Framework\Validator\EmailAddress|null $emailValidator
     * @param \Magento\Customer\Api\CustomerRepositoryInterface|null $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Newsletter\Model\SubscriptionManagerInterface $subscriptionManager,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\Framework\Validator\EmailAddress $emailValidator = null,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository = null
    ) {
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl,
            $customerAccountManagement,
            $subscriptionManager,
            $emailValidator,
            $customerRepository
        );
    }

    /**
     * New subscription action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $result['status'] = false;
        if ($this->getRequest()->isAjax() && $this->getRequest()->getPost('email')) {
            $email = (string)$this->getRequest()->getPost('email');

            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

                $subscriber = $this->_subscriberFactory->create()->loadByEmail($email);
                if ($subscriber->getId()
                    && (int) $subscriber->getSubscriberStatus() === Subscriber::STATUS_SUBSCRIBED
                ) {
                    throw new LocalizedException(
                        __('This email address is already subscribed.')
                    );
                }

                $subscriber = $this->_subscriberFactory->create();
                $subscriber->setImportMode(true);
                $status = (int) $subscriber->subscribe($email);
                $firstname = $this->getRequest()->getPost('firstname');
                $lastname = $this->getRequest()->getPost('lastname');
                $subscriber->setSubscriberFirstname($firstname);
                $subscriber->setSubscriberLastname($lastname);
                $subscriber->save();

                $result['message'] = $this->getSubscriberSuccessMessage($status);
                $result['status'] = true;
            } catch (LocalizedException $e) {
                $result['message'] = $e->getMessage();
            } catch (\Exception $e) {
                $result['message'] = __('Something went wrong with the subscription.');
            }
        }
        $this->getResponse()->representJson(
            $this->jsonSerializer->serialize($result)
        );
        return;
    }

    /**
     * Get success message
     *
     * @param int $status
     * @return Phrase
     */
    private function getSubscriberSuccessMessage($status)
    {
        if ($status === Subscriber::STATUS_NOT_ACTIVE) {
            return __('The confirmation request has been sent.');
        }

        return __('Thank you for your subscription.');
    }
}
