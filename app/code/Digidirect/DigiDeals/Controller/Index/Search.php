<?php

namespace Digidirect\DigiDeals\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;

class Search extends Action {
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }   

    public function execute() {
        
        $result = $this->_resultJsonFactory->create();
        
        if ($this->getRequest()->getParam('innerSearchInput')) {
            $result->setData(['output' => $this->getRequest()->getParam('innerSearchInput')]);
            return $result;
        } else {
            $result->setData(['output' => 'No data!']);
            return $result;
        }
        
    }

}
