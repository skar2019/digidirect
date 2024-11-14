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
    
    protected $logger;
    
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_productRepository = $productRepository;
        $this->logger = $logger;
    }   

    public function execute() {
        
        $result = $this->_resultJsonFactory->create();
        $id = $this->getRequest()->getParam('id');
        $this->logger->info('$id', $id);
        
        if ($id) {
            $product = $this->_productRepository->getById($id);
            $finalPrice = $product->getData('final_price');
            $wiserPrice = $product->getData('wiser_price');
            $basePrice = $product->getPrice();
            $discountWiserPrice = round($basePrice - $wiserPrice, 2);
            $this->logger->info('$finalPrice', $finalPrice);
            $this->logger->info('$wiserPrice', $wiserPrice);
            $this->logger->info('$basePrice', $basePrice);
            $this->logger->info('$discountWiserPrice', $discountWiserPrice);
            
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
