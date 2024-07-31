<?php

namespace Digidirect\ParticularAudienceAPI\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory; 
use Magento\Checkout\Model\Cart;

class FrequentlyBoughtWith extends Action {
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
    
    protected $formKey;
    
    protected $cart;
    
    protected $product;
    
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Data\Form\FormKey $formKey, 
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        parent::__construct($context);
    }   

    public function execute() {
        
        $result = $this->_resultJsonFactory->create();
        
        $productIds = $this->getRequest()->getParam('productIds');
        
        if ($productIds) {
            
            foreach($products as $item){
                $params = array(
                    'form_key'  => $this->formKey->getFormKey(),
                    'product'   => $item,
                    'qty'       => 1               
                );
                $productToAdd = $this->product->load($item);       
                $this->cart->addProduct($productToAdd, $params);
            }     
            $this->cart->save();
            
            $result->setData(['result' => 'Success!']);
            return $result;
            
        } else {
            
            $result->setData(['result' => 'No Products!']);
            return $result;
            
        }
        
    }

}
