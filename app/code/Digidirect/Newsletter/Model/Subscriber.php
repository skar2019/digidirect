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
        return $this->_updateCustomerSubscription($customerId, true);
    }

}
