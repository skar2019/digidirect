<?php 
namespace Digidirect\PaSalesForceProductRecommendation\Controller\Index;
use Digidirect\PaSalesForceProductRecommendation\Model\DataExampleFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class Index extends \Magento\Framework\App\Action\Action{
    protected $_dataExample;
    protected $resultRedirect;

    protected $_customerSession;

    protected $_customerRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Digidirect\PaSalesForceProductRecommendation\Model\DataExampleFactory  $dataExample,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession)
    {
            parent::__construct($context);
            $this->_dataExample = $dataExample;
            $this->resultRedirect = $result;
            $this->_customerSession = $customerSession;
            $this->_customerRepository = $customerRepository;
    }

	public function execute()
    {

        $pa_id = "dito dapat";
        $customerID =  $this->_customerSession->getCustomer()->getId(); //Print current customer ID

        $customer = $this->_customerRepository->getById($customerID);
        $customer->setCustomAttribute('pa_id',$pa_id);
        $this->_customerRepository->save($customer);

	}
}
 ?>