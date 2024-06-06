<?php

namespace Digidirect\SftpDownload\Block\Adminhtml;


class Content extends \Magento\Framework\View\Element\Template {
    
    protected $directoryList;
    
    protected $driverFile;
    
    protected $logger;
    
    public  function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->directoryList = $directoryList;
        $this->driverFile = $driverFile;
        $this->logger = $logger;
    }
    
    public function testEcho() {
        echo 'Send PDF!';
    }

    public function getFileContents() {
        $paths = [];
        try {
            $path = $this->directoryList->getPath(DirectoryList::VAR_DIR). '/export/email/digi_website_au_customers_10_05_2022_025147.csv';
            //read just that single directory
            $contents =  $this->driverFile->fileGetContents($path);
        } catch (FileSystemException $e) {
            $this->logger->error($e->getMessage());
        }

        return $contents;
    }
    
}
