<?php
/**
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Newsletter\Helper\Data as NewsletterHelper;
use Magento\Newsletter\Model\Subscriber as NewsletterSubscriber;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Newsletterpopup\Helper\Config;
use Plumrocket\Newsletterpopup\Helper\Data;

class Subscriber extends NewsletterSubscriber
{
    protected $_subscriberEncoded;
    protected $_dataHelper;
    protected $_messageManager;
    protected $_attributeMetadataDataProvider;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\HistoryManagement
     */
    private $historyManagement;

    /**
     * @var \Plumrocket\Newsletterpopup\Helper\Config
     */
    private $config;
    
    protected $curl;
    
    protected $jsonSerializer;

    public function __construct(
        Context $context,
        Registry $registry,
        NewsletterHelper $newsletterData,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        StateInterface $inlineTranslation,
        SubscriberEncoded $subscriberEncoded,
        Data $dataHelper,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        ManagerInterface $messageManager,
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        HistoryManagement $historyManagement = null,
        Config $config = null
    ) {
        $this->_subscriberEncoded = $subscriberEncoded;
        $this->_dataHelper = $dataHelper;
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
        $this->_messageManager = $messageManager;
        $this->_attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->historyManagement = $historyManagement ?: ObjectManager::getInstance()->get(HistoryManagement::class);
        $this->config = $config ?: ObjectManager::getInstance()->get(Config::class);
        parent::__construct(
            $context,
            $registry,
            $newsletterData,
            $scopeConfig,
            $transportBuilder,
            $storeManager,
            $customerSession,
            $customerRepository,
            $customerAccountManagement,
            $inlineTranslation,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function customSubscribe($email, $controller, $data = [])
    {
        $customer = $this->_subscriberEncoded->validateCustomer($data);
        if ($customer === false) {
            return false;
        }

        $address = $this->_subscriberEncoded->validateAddress($data);
        if ($address === false) {
            return false;
        }

        if ($customerId = $this->_subscriberEncoded->tryRegisterCustomer($customer, $controller)) {
            $saveAddress = true;
            $systemItems = $this->_dataHelper->getPopupFormFields(0, false);
            foreach ($systemItems as $name => $value) {
                if (! $address->getData($name)
                    && $this->_attributeMetadataDataProvider->getAttribute('customer_address', $name)->getIsRequired()
                ) {
                    $saveAddress = false;
                    break;
                }
            }

            if ($saveAddress) {
                // save address
                $address->setCustomerId($customerId)
                    ->setIsDefaultBilling(false)
                    ->setIsDefaultShipping(false);

                $address->save();
            }
        }

        $status = $this->subscribe($email);

        // Fix for the subcriber update in 2.4, we check the loadBySubscriberEmail method to split old and new logic.
        if (method_exists($this, 'loadBySubscriberEmail')) {
            $websiteId = (int)$this->_storeManager->getStore()->getWebsiteId();
            $subscriber = $this->loadBySubscriberEmail($email, $websiteId);
            $subscriber->addData(
                $this->_subscriberEncoded->getAdditionalData($data)
            );
            $subscriber->save();
        } else {
            $this->addData(
                $this->_subscriberEncoded->getAdditionalData($data)
            );
            $this->save();
        }

        if ($status == self::STATUS_NOT_ACTIVE) {
            $this->_subscriberEncoded->holdSubscribe($email, $data);
            $this->_messageManager->addSuccessMessage(
                __('Thank you for subscribing to our newsletter! Confirmation request has been sent.')
            );
        } else {
            $this->_subscriberEncoded->subscribe($email, $data);
            if ($successText = $this->_subscriberEncoded->getPopup()->getPreparedTextSuccess()) {
                $this->_messageManager->addSuccessMessage($successText);
            }
        }
        
        $getTokenUrl = 'https://digidirect2022.my.salesforce.com/services/oauth2/token';
        $getTokenParams = ["grant_type"=>"password","username"=>"sfdc.connect@digidirect.com.au","password"=>"idv5EdQ3cNYG1zuF3pje!inXRgbsxaaQRzbjWCnllpWZ0z","client_id"=>"3MVG9wt4IL4O5wvKHkw4LwXtVE2s.EYz9zxXLdFQ_F5LhhQQ9dRSWJEvkcyWje6OFpVm3qOLjsWVBjJVUy26z","client_secret"=>"CEEF6DD5884CF7C8DA8089015A1438F089B9B729A2DA0CEC9F63E1003B63D9B9"];

        $getTokenCurl = $this->curl;
        $getTokenCurl->addHeader("Content-Type", "application/x-www-form-urlencoded");
        $getTokenCurl->post($getTokenUrl, $getTokenParams);

        $getTokenResult = $getTokenCurl->getBody();

        $getTokenJson = $this->jsonSerializer->unserialize($getTokenResult);
        //echo $this->console_log('$getTokenJson: ' . json_encode($getTokenJson));
        
        return $status;
    }
    
    public function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
            ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }

    /**
     * @deprecated since 4.6.0
     * @see \Plumrocket\Newsletterpopup\Model\HistoryManagement::logCanceling
     */
    public function cancel()
    {
        if (! $this->config->isModuleEnabled() || $this->_dataHelper->isAdmin()) {
            return;
        }
        $popup = $this->_subscriberEncoded->getPopup();
        if (! $popup->getId()) {
            return;
        }
        $this->historyManagement->logCanceling($popup);
    }

    public function confirm($code)
    {
        $result = parent::confirm($code);
        if ($result && $this->config->isModuleEnabled()) {
            $this->_subscriberEncoded->releaseSubscribe($this);
        }
        return $result;
    }

    public function sendConfirmationSuccessEmail()
    {
        if ($this->_subscriberEncoded->getPopup()->getSendEmail()) {
            if ($this->config->isModuleEnabled()
                && $this->_subscriberEncoded->getPopup()->getId() > 0
            ) {
                return $this;
            }
            return parent::sendConfirmationSuccessEmail();
        }

        return $this;
    }
}
