<?php
namespace Digidirect\FilterShipping\Plugin\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;

class ShippingMethodManagement {
   /**
    * @var GetSourceItemsBySku
    */
   private $getSourceItemsBySku;

   /**
    * @var Cart
    */
   private $cart;

   /**
    * @var ProductRepositoryInterface
    */
   private $productRepository;
   
   public function __construct(
       GetSourceItemsBySku $getSourceItemsBySku,
       Cart $cart,
       ProductRepositoryInterface $productRepository
   ) {
       $this->getSourceItemsBySku = $getSourceItemsBySku;
       $this->cart = $cart;
       $this->productRepository = $productRepository;
   }

   public function afterEstimateByExtendedAddress($shippingMethodManagement, $output)
   {
       return $this->filterOutput($output);
   }
   private function filterOutput($output)
   {
       $items = $this->cart->getQuote()->getAllItems();

       $stockArray = [];
       
       foreach ($items as $item) {
           
           $qty = 0;
           $prodId = $item->getProductId();
           $product = $this->productRepository->getById($prodId);

           $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());

           foreach ($sourceItems as $sourceItemId => $sourceItem) {

               $qty .= $sourceItem->getQuantity();
           }
           
           $stockArray[] = $qty;
       }
       
       $showMethod = [];
       foreach ($output as $shippingMethod) {
           
           if ($shippingMethod->getCarrierCode() == 'shipping') {
               $showMethod[] = $shippingMethod;
           }
           
       }
       if ($showMethod) {
           
           if(in_array(0, $stockArray)){
               return $showMethod;
           }
           
       }
       return $output;
     
       
       
   }
}
