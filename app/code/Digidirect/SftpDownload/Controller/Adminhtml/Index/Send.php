<?php

namespace Digidirect\SftpDownload\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory; 

class Send extends Action {
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
        
        if ($this->getRequest()->getParam('email')) {
            $result->setData(['output' => $this->getRequest()->getParam('email')]);
            return $result;
        }
        
    }

}
