<?php
namespace Digidirect\Newsletter\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\App\Helper\AbstractHelper;

class Newsletter extends AbstractHelper implements \Magento\Framework\Event\ObserverInterface
{
    protected $customerRepository;
    
    protected $customerSession;
    
    protected $curl;
    
    protected $jsonSerializer;
    
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) 
    {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
    }
    
    public function execute(Observer $observer)
    {
        $subscriber = $observer->getEvent()->getSubscriber();
        $email = $subscriber->getEmail();
        $subscriberStatus = $subscriber->getSubscriberStatus();
        // subscriberStatus = 1 subscribe
        // subscriberStatus = 3 unsubscribed
        $customerID = $subscriber->getCustomerId();
        $customer = $this->customerRepository->getById($customerID);
        
        if ( $subscriberStatus == '1') {
            $customer->setCustomAttribute('marketing_consent', 1);
            $this->customerRepository->save($customer);
        } else {
            $customer->setCustomAttribute('marketing_consent', 0);
            $this->customerRepository->save($customer);
        }
        
        $getTokenUrl = 'https://digidirect2022.my.salesforce.com/services/oauth2/token';
        $getTokenParams = ["grant_type"=>"password","username"=>"sfdc.connect@digidirect.com.au","password"=>"idv5EdQ3cNYG1zuF3pje!inXRgbsxaaQRzbjWCnllpWZ0z","client_id"=>"3MVG9wt4IL4O5wvKHkw4LwXtVE2s.EYz9zxXLdFQ_F5LhhQQ9dRSWJEvkcyWje6OFpVm3qOLjsWVBjJVUy26z","client_secret"=>"CEEF6DD5884CF7C8DA8089015A1438F089B9B729A2DA0CEC9F63E1003B63D9B9"];
        
        $getTokenCurl = $this->curl;
        $getTokenCurl->addHeader("Content-Type", "application/x-www-form-urlencoded");
        $getTokenCurl->post($getTokenUrl, $getTokenParams);

        $getTokenResult = $getTokenCurl->getBody();

        $getTokenJson = $this->jsonSerializer->unserialize($getTokenResult);
        
        $webSignUpUrl = 'https://digidirect2022.my.salesforce.com/services/apexrest/WebSignup';
        $webSignParams = ["email"=>$email,"source"=>"Web"];

        $webSignUpCurl = $this->curl;
        $webSignUpCurl->addHeader("Content-Type", "application/JSON");
        $webSignUpCurl->addHeader("Authorization", "Bearer " . $getTokenJson['response']['access_token']);
        $webSignUpCurl->post($webSignUpUrl, $webSignParams);
        
        $webSignUpResult = $webSignUpCurl->getBody();
        
    }
}