<?php

namespace Digidirect\Customer\Observer;

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
        /*echo "Customer LoggedIn";
        $customer = $observer->getEvent()->getCustomer();
        echo $customer->getName(); //Get customer name
        exit;*/
        
        $customer = $observer->getCustomer();
        if(!$customer instanceof \Magento\Customer\Model\Customer){
            $customer = $this->customerFactory->create()->load($customer->getId());
        }

        /* Save customer Custom*/
        /*$controller = $observer->getAccountController();
        $family_name = $controller->getRequest()->getParam('pa_customer_id');*/

        $customer->setData('pa_customer_id', 'Testing!')->save();
        
        $customer = $this->customerRepository->getById($customer->getId());
        $customer->setCustomAttribute('pa_customer_id', 'Testing!');
        $this->customerRepository->save($customer);
    }
}