<?php

namespace Digidirect\DigiClub\Controller\Customer;

class Index extends \Magento\Framework\App\Action\Action 
{
    protected $_customerSession;
    
    public function __construct
    (
        \Magento\Customer\Model\Session $customerSession,
    ) {
        $this->_customerSession = $customerSession;
    }
    
    public function execute() 
    {
        $layout = $this->_view->loadLayout();
        $layout->getlayout()->getBlock('digiclub-tab')->setData('isDigiClub', $this->getGroupId());
        $this->_view->renderLayout();
    }
    
    public function getGroupId()
    {
        if($this->_customerSession->isLoggedIn()):
            $customerGroup = $this->_customerSession->getCustomer()->getGroupId();
            if($customerGroup == 10) {
                return true;
            } else {
                return false;
            }
        endif;
    }
}