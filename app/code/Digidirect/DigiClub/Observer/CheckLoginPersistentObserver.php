<?php

namespace Digidirect\DigiClub\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckLoginPersistentObserver implements ObserverInterface
{
    /**
    * @var \Magento\Framework\App\Response\RedirectInterface
    */
    protected $redirect;

    /**
    * Customer session
    *
    * @var \Magento\Customer\Model\Session
    */
    protected $_customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    ) {
        $this->_customerSession = $customerSession;
        $this->redirect = $redirect;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $actionName = $observer->getEvent()->getRequest()->getFullActionName();
        $controller = $observer->getControllerAction();

        if(!$this->_customerSession->isLoggedIn() && $controller == 'digiclub/customer/index') {
            $this->redirect->redirect($controller->getResponse(), 'customer/account/login');
        }
    }

}
