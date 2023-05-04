<?php 

namespace Digidirect\Customer\Controller\Account;

use Magento\Framework\Controller\ResultFactory;

class GetPaIdSalesForce extends \Magento\Framework\App\Action\Action {
    
    protected $request;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession
    ) 
    {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_customerRepository = $customerRepository;
    }

    public function execute(){
        if ($this->getRequest()->getPost('pa_id')):
            $pa_id = $this->getRequest()->getPost('pa_id');
            $data = array($pa_id);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($data);
            //return $resultJson;
            $customerID = $this->_customerSession->getCustomer()->getId();
            $customer = $this->_customerRepository->getById($customerID);
            $customer->setCustomAttribute('pa_customer_id', $pa_id);
            $this->_customerRepository->save($customer);
        endif;
    }
    
}