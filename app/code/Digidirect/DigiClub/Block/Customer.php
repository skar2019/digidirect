<?php

namespace Digidirect\Digiclub\Block;

use Magento\Customer\Model\Session;

class Customer {
    
    protected $customer;
    
    protected $customerSession;
        
    public function __construct(
        \Magento\Customer\Model\Session $customer,
        Session $session
    ) {        
        $this->customer = $customer;
        $this->customerSession = $session;
    }
    
    public function getCustomer() {
        $customer = $this->customer;
        return $customer;
    }
    
    public function getCustomerSession() {
        return $this->customerSession;
    }
    
}