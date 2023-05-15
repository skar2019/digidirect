<?php
namespace Digidirect\Newsletter\Observer;

use Magento\Framework\Event\Observer;

class Newsletter implements \Magento\Framework\Event\ObserverInterface
{
    protected $customerRepository;
    
    protected $customerSession;
    
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession
    ) 
    {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
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
        
    }
}