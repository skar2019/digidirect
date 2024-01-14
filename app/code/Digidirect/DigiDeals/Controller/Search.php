<?php

namespace Digidirect\DigiDeals\Controller;

use Magento\Framework\App\Action\Action;

class Search extends Action {
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
    }   

    public function execute() {

        if ($this->getRequest()->getParam('innerSearchInput')) {
            return $this->getRequest()->getParam('innerSearchInput');
        } else {
            return 'No data!';
        }
    }

}
