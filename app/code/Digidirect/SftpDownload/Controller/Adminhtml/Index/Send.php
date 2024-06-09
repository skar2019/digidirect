<?php

namespace Digidirect\SftpDownload\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory; 
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class Send extends Action {
    
    protected $_resultJsonFactory;
    
    protected $transportBuilder;
    
    protected $storeManager;
    
    protected $logger;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Psr\Log\LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        parent::__construct($context);
    }   

    public function execute() {
        
        $result = $this->_resultJsonFactory->create();
        $customerEmail = $this->getRequest()->getParam('email');
        
        $store = $this->storeManager->getStore();
        $templateParams = [];
        
        $transport = $this->transportBuilder->setTemplateIdentifier(
            'send_pdf_email_template'
            )->setTemplateOptions(
                ['area' => 'adminhtml', 'store' => $store->getId()]
            )->addTo(
                $customerEmail, $customerEmail
            )->setTemplateVars(
                $templateParams
            )->setFrom(
                'general'
            )->addBcc(
                'rondel.d@digidirect.com.au' 
            )->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        
        if ($customerEmail) {
            $result->setData(['output' => $customerEmail]);
            return $result;
        }
        
    }

}
