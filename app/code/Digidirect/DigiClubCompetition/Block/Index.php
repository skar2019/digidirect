<?php

namespace Digidirect\DigiClubCompetition\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;

class Index extends Template
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
    
    protected $customerRepository;
    
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        CustomerRepository $customerRepository
            
    ) {
        $this->_customerSession = $customerSession;
        $this->redirect = $redirect;
        $this->urlInterface = $urlInterface;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
    }
    
    public function checkCustomerSession() {
        $this->logger->info('checkCustomerSession');
        return $this->_customerSession->isLoggedIn();
    }
    
    public function checkCustomerGroup() {
        $this->logger->info('checkCustomerGroup');
        $customerId = $this->_customerSession->getCustomerId();
        $customer = $this->customerRepository->getById($customerId);
        $storeId = (int)$this->storeManager->getStore()->getId();
        $customer->setStoreId($storeId);
        $customerGroupId = $customer->getGroupId();
        
        return $customerGroupId;
    }

    /*public function test()
    {
        $controller = $observer->getControllerAction();
        $routeName = $observer->getEvent()->getRequest()->getRouteName();
        $name = $observer->getEvent()->getRequest()->getFullActionName();
        //$this->logger->info('getBaseUrl: ' . $this->urlInterface->getBaseUrl());
        $currentUrl = rtrim($this->urlInterface->getCurrentUrl(), '/');
        //$this->logger->info('currentUrl: ' . $currentUrl);
        $isRedirect = $observer->getEvent()->getRequest()->getParam('digiclub');
        
        //$this->logger->info('$isRedirect: ' . $isRedirect);
        
        if(!$this->_customerSession->isLoggedIn() && $routeName == 'digiclubmember') {
            $url = $this->urlInterface->getUrl('digiclubmember/customer/index');
            if ($isRedirect) {
                $url = $this->urlInterface->getUrl('digiclubmember/customer/index/digiclub/1');
            }
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
            
        } else if ($this->_customerSession->isLoggedIn() && $currentUrl == $this->urlInterface->getBaseUrl().'digiclubmember/customer/index/digiclub/1' && $this->_customerSession->getCustomer()->getGroupId() == 10) {
            $page_url = $this->urlInterface->getUrl('digiclub-member-deals');
            $this->redirect->redirect($controller->getResponse(), $page_url);
            //$this->logger->info('digiclub-member-deals');
            
        }
    }*/
    
    
}