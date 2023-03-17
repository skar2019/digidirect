<?php

namespace Vendor\Module\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class CustomerLogin implements ObserverInterface
{
    protected $customerFactory;
    protected $customerRepository;
    

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository
    ){
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        echo "Customer LoggedIn";
        $customer = $observer->getEvent()->getCustomer();
        echo $customer->getName(); //Get customer name
        exit;
    }
}