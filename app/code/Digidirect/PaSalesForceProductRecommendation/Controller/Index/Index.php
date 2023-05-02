<?php 

namespace Digidirect\PaSalesForceProductRecommendation\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action  {
    
    protected $request;
    public function __construct(Context $context,array $data = []) {
        parent::__construct($context,$data);
    }
    
    public function __execute() {
        if ($this->getRequest()->getPost('pa_id')):
            $query = $this->getRequest()->getPost('pa_id');
            $data = array($query);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($data);
            return $resultJson;
        endif;
    }
    
}