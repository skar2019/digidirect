<?php
namespace Digidirect\Newsletter\Plugin\Block;

class Subscribe
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
    
    public function beforeGetFormActionUrl(\Magento\Newsletter\Block\Subscribe $newsletter)
    {
        $customerID = $this->customerSession->getCustomer()->getId();
        $customer = $this->customerRepository->getById($customerID);
        $customer->setCustomAttribute('marketing_consent', 1);
        $this->customerRepository->save($customer);
        echo json_encode($customer);
    }
}