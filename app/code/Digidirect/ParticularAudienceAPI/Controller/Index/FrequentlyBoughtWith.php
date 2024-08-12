<?php

namespace Digidirect\ParticularAudienceAPI\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class FrequentlyBoughtWith extends Action implements HttpGetActionInterface {
    
   private $checkoutSession;

   private $cartRepository;

   private $productRepository;

   private $json;

   private $configurableType;

   public function __construct(
       Context $context,
       \Magento\Framework\Serialize\Serializer\Json $json,
       \Magento\Checkout\Model\SessionFactory $checkoutSession,
       \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
       \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
       \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
   ) {
       $this->checkoutSession = $checkoutSession;
       $this->cartRepository = $cartRepository;
       $this->productRepository = $productRepository;
       $this->json = $json;
       $this->configurableType = $configurableType;
       parent::__construct($context);
   }

   /**
    * @return ResultInterface
    * @throws LocalizedException
    */
    public function execute()
    {
         $productIds = $this->getRequest()->getParam('productIds');

         $session = $this->checkoutSession->create();
         $quote = $session->getQuote();

         foreach($productIds as $item) {
             $product = $this->productRepository->getById($item);
             $quote->addProduct($product, $qty);
         }

         $this->cartRepository->save($quote);
         $session->replaceQuote($quote)->unsLastRealOrderId();

         return true;
    }
}
