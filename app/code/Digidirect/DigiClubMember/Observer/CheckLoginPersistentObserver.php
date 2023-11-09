<?php

namespace Digidirect\DigiClubMember\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\Data\CategoryInterface;

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
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository
            
    ) {
        $this->_customerSession = $customerSession;
        $this->redirect = $redirect;
        $this->urlInterface = $urlInterface;
        $this->logger = $logger;
        $this->request = $request;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $routeName = $observer->getEvent()->getRequest()->getRouteName();
        $name = $observer->getEvent()->getRequest()->getFullActionName();
        
        if(!$this->_customerSession->isLoggedIn() && $routeName == 'digiclubmember') {
            $url = $this->urlInterface->getUrl('digiclubmember/customer/index');
            $login_url = $this->urlInterface->getUrl('customer/account/login', ['referer' => base64_encode($url), 'digiclub' => true]);
            $this->redirect->redirect($controller->getResponse(), $login_url);
            
        }
        
        if ($name == "catalog_category_view") {
            
            $category = $this->registry->registry('current_category');
            $categoryId = $category->getId();
            
            $this->logger->info('categoryId: ' . $categoryId);
            
            if (!$this->_customerSession->isLoggedIn() && $this->getCategoryUrl($categoryId) == 'digiclub-member-deals') {
                $url = $this->urlInterface->getUrl('digiclub-member-deals');
                $page_url = $this->urlInterface->getUrl('customer/account/login', ['referer' => base64_encode($url)]);
                $this->redirect->redirect($controller->getResponse(), $page_url);
            }
        }
    }
    
    public function getCategoryUrl($categoryId)
    {
        $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
        $this->logger->info('categoryUrl: ' . $category->getUrl());
        return $category->getUrl();
    }

}
