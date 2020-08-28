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
         
       $customerEmail = $order->getCustomerEmail();
      
       $customer = $this->_customerRepository->get($customerEmail);
       
       $getQffNumber = $customer->getQffNumber();
       
       $getQffLastName = $customer->getQffLastName();
       
       if  ($getQffNumber == NULL  && $getQffLastName == NULL  ){
       
          $order->setQffNumber('NULL')->save();  
           
           $order->setQffLastName('NULL')->save(); 
           

         
           return $this;
       }   
          
       if ( $getQffNumber !== NULL && $getQffLastName !== NULL){
 
             $order->setQffLastname($getQffNumber)->save();
            
            
             $order->setQffNumber($getQffLastName)->save();
            
            
           return $this;
     }
        
       
   }
    
    
    
}