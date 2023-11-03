<?php

namespace Digidirect\Digiclub\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Session\SessionManager;

class Customer extends Template {
    
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
        $this->logger->info('account firstname: ' . $this->customerSession->getData("firstname"));
        $this->logger->info('account lastname: ' . $this->customerSession->getData("lastname"));
        $this->logger->info('account email: ' . $this->customerSession->getEmail());
        $this->logger->info('account contact_number: ' . $this->customerSession->getData("contact_number"));
        $this->logger->info('account dob: ' . $this->customerSession->getData("dob"));
        return $this->customerSession;
    }
    
    public function getSessionManager() {
        $this->logger->info('account Session ID: ' . $this->sessionManager->getSessionId());
        return $this->sessionManager;
    }
    
}