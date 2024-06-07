<?php

namespace Digidirect\SftpDownload\Block\Adminhtml;

use Magento\Framework\View\Element\Template;

class Content extends Template {
    
    protected $directoryList;
    
    protected $driverFile;
    
    protected $logger;
    
    public function __construct(
        Template\Context $context, 
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Psr\Log\LoggerInterface $logger,
        array $data = []    
    )
    {
        $this->directoryList = $directoryList;
        $this->driverFile = $driverFile;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout() {
        return parent::_prepareLayout();
    }
    
    public function testEcho() {
        echo 'Send PDF!';
    }
    
    public function getFileContents() {
        $paths = [];
        try {
            $path = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR). '/export/email/digi_website_au_customers_10_05_2022_025147.csv';
            //read just that single directory
            $contents = $this->driverFile->fileGetContents($path);
        } catch (FileSystemException $e) {
            $this->logger->error($e->getMessage());
        }

        return $contents;
    }
    
}
