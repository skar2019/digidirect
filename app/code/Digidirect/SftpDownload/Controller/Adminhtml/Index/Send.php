<?php

namespace Digidirect\SftpDownload\Controller\Adminhtml\Index;

use Magento\Framework\Filesystem\Io\Sftp;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory; 
use Magento\Store\Model\StoreManagerInterface;

class Send extends Action {
    
    protected $sftp;

    protected $file;

    protected $directoryList;
    
    protected $_resultJsonFactory;
    
    protected $transportBuilder;
    
    protected $storeManager;
    
    protected $logger;
    
    public function __construct(
        Sftp $sftp,
        File $file,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->sftp = $sftp;
        $this->file = $file;
        $this->directoryList = $directoryList;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        return parent::__construct($context);
    }   

    public function execute() {
        
        $invoiceType = $this->getRequest()->getParam('invoiceType');
        $invoiceNumber = $this->getRequest()->getParam('invoiceNumber');
        
        $fileName = $invoiceType . " - " . $invoiceNumber . ".pdf";
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR) . '/export/'. $fileName;
        $targetFile = 'sftp://magentosftp@119.82.149.212:6999/MAGENTO/'.$fileName;

        $sftpConfig = [
            'host' => '119.82.149.212',
            'port' => '6999',
            'username' => 'magentosftp',
            'password' => 'CsUd!1G9#mdEui$z1zG#k94%xXk!0DZq'
        ];

        try {
            $this->sftp->open($sftpConfig);
            $this->sftp->cd('/MAGENTO/');

            //Fetching/Listing all the files.
            /*$sftp_server_files = $this->sftp->ls();
            foreach ($sftp_server_files as $file) {
                $source = $file['text'];
                echo $source . "\n";
            }*/

            $result = $this->sftp->read($fileName, $filePath);
            //$this->sftp->write($targetFile, $filePath);
            //$this->sftp->close();
            $this->logger->info($fileName . ', ' . $filePath);
            if($result == true) {
                $this->logger->info('File read from SFTP server');
            }
            else
            {
                $this->logger->info('File not able to read from SFTP server');
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
        
        
        
        
        $result = $this->_resultJsonFactory->create();
        $customerEmail = $this->getRequest()->getParam('email');
        
        $store = $this->storeManager->getStore();
        $templateParams = [];
        
        $pdfFile = '/app/6ycwrjafqqprk/var/export/'.$fileName;
        
        $transport = $this->transportBuilder->setTemplateIdentifier(
            'send_pdf_email_template'
            )->setTemplateOptions(
                ['area' => 'adminhtml', 'store' => $store->getId()]
            )->addTo(
                $customerEmail, $customerEmail
            )->addAttachment(
                file_get_contents($pdfFile), $fileName, 'application/pdf' 
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
        
        if ($fileName) {
            $result->setData(['output' => $fileName]);
            return $result;
        }
        
    }

}
