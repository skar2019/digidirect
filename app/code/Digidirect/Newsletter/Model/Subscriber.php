<?php

namespace Digidirect\Newsletter\Model;

class Subscriber extends \Magento\Newsletter\Model\Subscriber
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
   
    public function subscribe($email)
    {
        $storeId = (int)$this->_storeManager->getStore()->getId();
        $subscriber = $this->subscriptionManager->subscribe($email, $storeId);
        $this->addData($subscriber->getData());
        
        $customerID = $this->customerSession->getCustomer()->getId();
        $customer = $this->customerRepository->getById($customerID);
        $customer->setCustomAttribute('marketing_consent', 1);
        $this->customerRepository->save($customer);

        return $this->getStatus();
    }

    public function subscribeCustomerById($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        $customer->setCustomAttribute('marketing_consent', 1);
        $this->customerRepository->save($customer);
        
        return $this->_updateCustomerSubscription($customerId, true);
    }

}
