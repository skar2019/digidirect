<?php

namespace Digidirect\Digiclub\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use \Magento\Framework\Session\SessionManager;

class Customer extends Template {
    
    protected $customer;
    
    protected $customerSession;
    
    protected $sessionManager;
    
    protected $logger;
        
    public function __construct(
        Session $session,
        SessionManager $sessionManager,
        \Psr\Log\LoggerInterface $logger
    ) {        
        $this->customerSession = $session;
        $this->sessionManager = $sessionManager;
        $this->logger = $logger;
    }
    
    public function getCustomerSession() {
        $logger->info('account Session ID: ' . $this->customerSession->getSessionId());
        $logger->info('account firstname: ' . $this->customerSession->getData("firstname"));
        $logger->info('account lastname: ' . $this->customerSession->getData("lastname"));
        $logger->info('account email: ' . $this->customerSession->getEmail());
        $logger->info('account contact_number: ' . $this->customerSession->getData("contact_number"));
        $logger->info('account dob: ' . $this->customerSession->getData("dob"));
        return $this->customerSession;
    }
    
    public function getSessionManager() {
        $logger->info('account Session ID: ' . $this->sessionManager->getSessionId());
        return $this->sessionManager;
    }
    
}