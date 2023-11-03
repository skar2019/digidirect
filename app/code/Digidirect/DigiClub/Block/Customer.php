<?php

namespace Digidirect\Digiclub\Block;

use Magento\Customer\Model\Session;

class Customer {
    
    protected $customer;
    
    protected $customerSession;
        
    public function __construct(
        Session $session
    ) {        
        $this->customerSession = $session;
    }
    
    public function getCustomerSession() {
        return $this->customerSession;
    }
    
}