<?php

namespace Digidirect\Digiclub\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;

class Customer extends Template {
    
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