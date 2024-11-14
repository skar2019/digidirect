<?php

namespace Digidirect\Algolia\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory; 

class FinalPrice extends Action {
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    
    protected $_productRepository;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }   

    public function execute() {
        
        $result = $this->_resultJsonFactory->create();
        $id = $this->getRequest()->getParam('id');
        
        if ($id) {
            $product = $this->_productRepository->getById($id);
            $finalPrice = $product->getData('final_price');
            $wiserPrice = $product->getData('wiser_price');
            $basePrice = $product->getPrice();
            $discountWiserPrice = round($basePrice - $wiserPrice, 2);
            
            if ($wiserPrice == 0 || empty($wiserPrice) || $discountWiserPrice < 10) {
                $result->setData($finalPrice);
            } else {
                if ($wiserPrice < $finalPrice) {
                    $result->setData($wiserPrice);
                } else {
                    $result->setData($finalPrice);
                }
            }
            
            return $result;
        }
        
    }

}
