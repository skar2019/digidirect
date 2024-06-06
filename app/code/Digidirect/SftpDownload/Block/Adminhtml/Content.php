<?php

namespace Digidirect\SftpDownload\Block\Adminhtml;

use Magento\Framework\View\Element\Template;

class Content extends Template {
    
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
    
}
