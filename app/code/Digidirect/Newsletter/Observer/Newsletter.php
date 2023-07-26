<?php
namespace Digidirect\Newsletter\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\App\Helper\AbstractHelper;

class Newsletter implements \Magento\Framework\Event\ObserverInterface
{
    protected $customerRepository;
    
    protected $customerSession;
    
    protected $curl;
    
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) 
    {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->curl = $curl;
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
        
        /*$url = 'https://digidirect2022.my.salesforce.com/services/oauth2/token';

        $this->curl->addHeader("Content-Type", "application/x-www-form-urlencoded");
        $this->curl->setOption("grant_type", "password");
        $this->curl->setOption("username", "sfdc.connect@digidirect.com.au");
        $this->curl->setOption("password", "idv5EdQ3cNYG1zuF3pje!inXRgbsxaaQRzbjWCnllpWZ0z");
        $this->curl->setOption("client_id","3MVG9wt4IL4O5wvKHkw4LwXtVE2s.EYz9zxXLdFQ_F5LhhQQ9dRSWJEvkcyWje6OFpVm3qOLjsWVBjJVUy26z");
        $this->curl->setOption("client_secret", "CEEF6DD5884CF7C8DA8089015A1438F089B9B729A2DA0CEC9F63E1003B63D9B9");
        $this->curl->get($url);

        $result = $this->curl->getBody();

        $json = $this->jsonSerializer->unserialize($result);
        var_dump($json);*/
        
    }
}