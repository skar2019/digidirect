<?php 

namespace Digidirect\Customer\Controller\Account;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;

class GetPaIdSalesForce extends \Magento\Framework\App\Action\Action {
    
    protected $request;
    public function __construct(Context $context) {
        parent::__construct($context);
    }

    public function execute(){
        echo $this->getRequest()->getPost('pa_id');
        if ($this->getRequest()->getPost('pa_id')):
            $query = $this->getRequest()->getPost('pa_id');
            $data = array($query);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($data);
            return $resultJson;
        endif;
    }
    
}