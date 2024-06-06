<?php

namespace Digidirect\SftpDownload\Block\Adminhtml;


class Content extends \Magento\Framework\View\Element\Template {
    
    protected $directoryList;
    
    protected $driverFile;
    
    protected $logger;
    
    public function __construct(
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
    
}
