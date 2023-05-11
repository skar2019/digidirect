<?php
namespace Digidirect\Newsletter\Plugin\Block;

class Subscribe
{
    protected $customerRepository;
    
    protected $customerSession;
    
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession
    ) 
    {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }
    
    public function beforeGetFormActionUrl(\Magento\Newsletter\Block\Subscribe $newsletter)
    {
        $customerID = $this->customerSession->getCustomer()->getId();
        $customer = $this->customerRepository->getById($customerID);
        $customer->setCustomAttribute('marketing_consent', 1);
        $this->customerRepository->save($customer);
        echo $this->console_log('Customer : ' . $customer);
    }
    
    public function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
            ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }
}