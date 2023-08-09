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
        //$actionName = $observer->getEvent()->getRequest()->getFullActionName();
        $controller = $observer->getControllerAction();
        $routeName = $observer->getEvent()->getRequest()->getRouteName();

        if(!$this->_customerSession->isLoggedIn() && $routeName == 'digiclub') {
            //$redirectUrl = $this->redirect->getRefererUrl();
            //$this->redirect->redirect($controller->getResponse(), 'customer/account/login');
            $url = $this->urlInterface->getBaseUrl() . '/digiclub/customer/index/'; //$this->redirect->getRefererUrl();
            $login_url = $this->urlInterface
                ->getUrl('customer/account/login',
                    array('referer' => base64_encode($url))
                );
            //$resultRedirect = $this->resultRedirectFactory->create();
            //$resultRedirect->setUrl($login_url);
            //return $resultRedirect;
            $this->redirect->redirect($controller->getResponse(), $login_url);
            //$redirectUrl = $this->urlInterface->getUrl('customer/account/login', array('referer'=>base64_encode($referenceUrl)));
            //return $redirectUrl;
        }
        
        
    }

}
