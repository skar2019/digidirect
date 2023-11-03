<?php

namespace Digidirect\Digiclub\Block;

class Customer {
    
    protected $customer;
        
    public function __construct(
        \Magento\Customer\Model\Session $customer
    ) {        
        $this->customer = $customer;
    }
    
    public function getCustomer() {
        $customer = $this->customer;
        return $customer;
    }
    
}