<?php
namespace Digi\Order\Observer;

class SetOrderAttribute implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;
    
    
    
    protected $customerSession;
    
    
    /**
    * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
            \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        
    }
    
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       
        /** @var \Magento\Sales\Model\Order $order */
       $order = $observer->getOrder();
         
       $customerEmail= $order->getCustomerEmail();
      
       $customer = $this->_customerRepository->get($customerEmail);
       
       $getQff = $customer->getQffNumber();
       
       
       if  ($getQff == NULL ){
           
          
           $saveQff = $order->setQffNumber('NULL');  
           
          
           $saveQff->save();
           return $this;
       }   
            
       if ( $getQff !== NULL){
    
           $saveQff = $order->setQffNumber($getQff);  
           
          
           $saveQff->save();
         
           return $this;
     }
        
       
   }
    
    
    
}