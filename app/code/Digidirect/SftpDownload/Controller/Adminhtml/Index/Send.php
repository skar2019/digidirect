<?php

namespace Digidirect\SftpDownload\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory; 

class Send extends Action {
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
    }   

    public function execute() {
        
        if ($this->getRequest()->getParam('email')) {
            $result->setData(['output' => $this->getRequest()->getParam('email')]);
            return $result;
        }
        
    }

}
