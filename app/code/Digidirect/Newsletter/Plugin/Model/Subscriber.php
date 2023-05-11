<?php
namespace Digidirect\Newsletter\Plugin\Model;

class Subscriber
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
        
    public function beforeSubscribe(\Magento\Newsletter\Model\Subscriber $newsletterEmail)
    {
        $customerID = $this->customerSession->getCustomer()->getId();
        $customer = $this->customerRepository->getById($customerID);
        $customer->setCustomAttribute('marketing_consent', 1);
        $this->customerRepository->save($customer);
    }
    
    
    public function beforeSubscribeCustomerById(\Magento\Newsletter\Model\Subscriber $newsletter, $customerId)
    {
        //$customerID = $this->customerSession->getCustomer()->getId();
        $customer = $this->customerRepository->getById($customerId);
        $customer->setCustomAttribute('marketing_consent', 1);
        $this->customerRepository->save($customer);
    }
}