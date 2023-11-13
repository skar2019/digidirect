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
    
    protected $logger;
    
    protected $registry;
    
    protected $request;
    
    protected $storeManager;
    
    protected $categoryRepository;
    

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\UrlInterface $urlInterface,
        \Psr\Log\LoggerInterface $logger
            
    ) {
        $this->_customerSession = $customerSession;
        $this->redirect = $redirect;
        $this->urlInterface = $urlInterface;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $routeName = $observer->getEvent()->getRequest()->getRouteName();
        $name = $observer->getEvent()->getRequest()->getFullActionName();
        $this->logger->info('getBaseUrl: ' . $this->urlInterface->getBaseUrl());
        $currentUrl = rtrim($this->urlInterface->getCurrentUrl(), '/');
        $this->logger->info('currentUrl: ' . $currentUrl);
        
        if(!$this->_customerSession->isLoggedIn() && $routeName == 'digiclubmember') {
            $url = $this->urlInterface->getUrl('digiclubmember/customer/index');
            $login_url = $this->urlInterface->getUrl('customer/account/login', ['referer' => base64_encode($url), 'digiclub' => true]);
            $this->redirect->redirect($controller->getResponse(), $login_url);

        } else if (!$this->_customerSession->isLoggedIn() && $currentUrl == $this->urlInterface->getBaseUrl().'digiclub-member-deals') {
            $url = $this->urlInterface->getUrl('digiclub-member-deals');
            $page_url = $this->urlInterface->getUrl('customer/account/login', ['referer' => base64_encode($url), 'digiclub' => true]);
            $this->redirect->redirect($controller->getResponse(), $page_url);
            
        } else if ($this->_customerSession->isLoggedIn() && $currentUrl == $this->urlInterface->getBaseUrl().'digiclub-member-deals' && $this->_customerSession->getCustomer()->getGroupId() != 10) {
            $url = $this->urlInterface->getUrl('digiclub-member-deals');
            $page_url = $this->urlInterface->getUrl('digiclubmember/customer/index', ['referer' => base64_encode($url)]);
            $this->redirect->redirect($controller->getResponse(), $page_url);
        }
    }

}
