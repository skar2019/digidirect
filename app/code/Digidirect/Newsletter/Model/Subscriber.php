<?php

namespace Digidirect\Newsletter\Model;

class Subscriber extends \Magento\Newsletter\Model\Subscriber
{
    public function subscribe($email)
    {
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
