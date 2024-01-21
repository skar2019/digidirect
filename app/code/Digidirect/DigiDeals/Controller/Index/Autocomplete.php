<?php

namespace Digidirect\DigiDeals\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory; 

class Autocomplete extends Action {
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }   

    public function execute() {
        
        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        
        if ($this->getRequest()->getParam('innerSearchInput')) {
            
            $data = array('searchTerm' => $this->getRequest()->getParam('innerSearchInput'));
 
            $block = $resultPage->getLayout()
            ->createBlock('Digidirect\DigiDeals\Block\Index\Autocomplete')
            ->setTemplate('Digidirect_DigiDeals::autocomplete.phtml')
            ->setData('data',$data)
            ->toHtml();
            
            $result->setData(['output' => $block]);
            return $result;
            
        }
    }

}
