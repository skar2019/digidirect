<?php

namespace Digidirect\SftpDownload\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory; 

class Send extends Action {
    
    protected $_resultJsonFactory;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }   

    public function execute() {
        
        $result = $this->_resultJsonFactory->create();
        if ($this->getRequest()->getParam('email')) {
            $result->setData(['output' => $this->getRequest()->getParam('email')]);
            return $result;
        }
        
    }

}
