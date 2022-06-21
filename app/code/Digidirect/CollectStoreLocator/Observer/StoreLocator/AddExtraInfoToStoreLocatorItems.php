<?php
namespace Digidirect\CollectStoreLocator\Observer\StoreLocator;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;
use Digidirect\CollectAbstractEntity\Helper\Places;
use Digidirect\Collect\Helper\Data;
use Digidirect\Collect\Api\CollectPlaceRepositoryInterface;

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
        Data $collectHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->placesHelper = $placesHelper;
        $this->collectHelper = $collectHelper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $transportObject = $observer->getTransportObject();
        $locatorStores = $transportObject->getData('items');
        echo $this->console_log("Before : " . $locatorStores);
        $locatorStores = $this->addAvailabilityInfoToItems($locatorStores);
        $transportObject->setData(['items' => $locatorStores]);
        echo $this->console_log("After : " . $locatorStores);
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

        foreach ($items as $key => $storeData) {
            $id = $storeData['entity_id'];
            if (empty($places[$id])) {
                continue;
            }

            $place = $places[$id];
            if ($place->hasData(CollectPlaceRepositoryInterface::KEY_IS_UNAVAILABLE)) {
                $items[$key]['available'] = true; // Andrew requested that all store is selectable; !$place->getData(CollectPlaceRepositoryInterface::KEY_IS_UNAVAILABLE);
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
