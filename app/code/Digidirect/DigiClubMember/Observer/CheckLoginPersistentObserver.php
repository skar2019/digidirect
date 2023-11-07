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
    
    protected $pageConfig;
    

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        $this->_customerSession = $customerSession;
        $this->redirect = $redirect;
        $this->urlInterface = $urlInterface;
        $this->pageConfig = $pageConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $routeName = $observer->getEvent()->getRequest()->getRouteName();

        if(!$this->_customerSession->isLoggedIn() && $routeName == 'digiclubmember') {
            $url = $this->urlInterface->getUrl('digiclubmember/customer/index');
            $login_url = $this->urlInterface
                ->getUrl('customer/account/login',
                    array('referer' => base64_encode($url))
                );
            $this->pageConfig->addBodyClass('digiclub-redirect');
            $this->redirect->redirect($controller->getResponse(), $login_url);
        }
    }

}
