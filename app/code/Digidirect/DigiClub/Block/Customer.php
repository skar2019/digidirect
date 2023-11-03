<?php

namespace Digidirect\Digiclub\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use \Magento\Framework\Session\SessionManager;

class Customer extends Template {
    
    protected $customer;
    
    protected $customerSession;
    
    protected $sessionManager;
        
    public function __construct(
        Session $session,
        SessionManager $sessionManager
    ) {        
        $this->customerSession = $session;
        $this->sessionManager = $sessionManager;
    }
    
    public function getCustomerSession() {
        return $this->customerSession;
    }
    
    public function getSessionManager() {
        return $this->sessionManager;
    }
    
}