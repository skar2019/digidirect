<?php
namespace Digidirect\CollectStoreLocator\Observer\StoreLocator;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;
use Digidirect\CollectAbstractEntity\Helper\Places;
use Digidirect\Collect\Helper\Data;
use Digidirect\Collect\Api\CollectPlaceRepositoryInterface;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;

/**
 * Class AddExtraInfoToStoreLocatorItems
 * @package Digidirect\CollectStoreLocator\Observer\StoreLocator
 */
class AddExtraInfoToStoreLocatorItems implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var Places
     */
    protected $placesHelper;

    /**
     * @var Data
     */
    protected $collectHelper;

    /**
     * AddExtraInfoToStoreLocatorItems constructor.
     * @param Session $checkoutSession
     * @param Places $placesHelper
     * @param Data $collectHelper
     */
    public function __construct(
        Session $checkoutSession,
        Places $placesHelper,
        Data $collectHelper,
        GetSourceItemsBySku $getSourceItemsBySku
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->placesHelper = $placesHelper;
        $this->collectHelper = $collectHelper;
        $this->getSourceItemsBySku = $getSourceItemsBySku;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $transportObject = $observer->getTransportObject();
        $locatorStores = $transportObject->getData('items');
        $locatorStores = $this->addAvailabilityInfoToItems($locatorStores);
        $transportObject->setData(['items' => $locatorStores]);
        echo $this->console_log($locatorStores);
    }

    /**
     * @param array $items
     * @return array
     */
    public function addAvailabilityInfoToItems($items)
    {
        $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $skuQty = $this->collectHelper->getSkuToQtyByItems($quoteItems);
        $places = $this->placesHelper->getAllCollectPlacesEntities($skuQty);
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $cartItems = $cart->getQuote()->getAllItems();

        foreach ($items as $key => $storeData) {
            
            $id = $storeData['entity_id'];
            $qty = 0;
            
            if (empty($places[$id])) {
                continue;
            }

            $place = $places[$id];
            if ($place->hasData(CollectPlaceRepositoryInterface::KEY_IS_UNAVAILABLE)) {
                $items[$key]['available'] = true; // Andrew requested that all store is selectable; !$place->getData(CollectPlaceRepositoryInterface::KEY_IS_UNAVAILABLE);
            }
            
            foreach ($cartItems as $cartItem) {
            
                $prodId = $cartItem->getProductId();
                $product = $objectManager->get('\Magento\Catalog\Model\Product')->load($prodId);

                $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());
                
                $sydnQty = 0;

                foreach ($sourceItems as $sourceItemId => $sourceItem) {
                    echo $this->console_log($sourceItem->getQuantity());
                    echo $this->console_log($sourceItem->getSourceCode());
                    //$qty .= $sourceItem->getQuantity();
                    
                    if ($id == 10 && $sourceItem->getSourceCode() == 'SYDN') {
                        $sydnQty = $sydnQty * $sourceItem->getQuantity();
                    }
                    
                    if ($id == 10 && $sourceItem->getSourceCode() == 'SYDN' && $sydnQty > 0) {
                        $items[$key]['available'] = true;
                    } else {
                        $items[$key]['available'] = false;
                    }
                }
            }
        }
        return $items;
    }
    
    function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
    ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }
}
