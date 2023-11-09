<?php

namespace Digidirect\DigiClubMember\Observer;

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
    
    protected $urlInterface;
    

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->_customerSession = $customerSession;
        $this->redirect = $redirect;
        $this->urlInterface = $urlInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $routeName = $observer->getEvent()->getRequest()->getRouteName();

        if(!$this->_customerSession->isLoggedIn() && $routeName == 'digiclubmember') {
            $url = $this->urlInterface->getUrl('digiclubmember/customer/index');
            $login_url = $this->urlInterface
                ->getUrl('customer/account/login', ['referer' => base64_encode($url), 'digiclub' => true]);
            $this->redirect->redirect($controller->getResponse(), $login_url);
        } elseif (!$this->_customerSession->isLoggedIn() && $routeName == 'digiclub-member-deals') {
            $url = $this->urlInterface->getUrl('digiclub-member-deals');
            $page_url = $this->urlInterface
                ->getUrl('customer/account/login', ['referer' => base64_encode($url)]);
            $this->redirect->redirect($controller->getResponse(), $page_url);
        }
    }

}
