<?php
namespace Digidirect\CollectStoreLocator\Observer\StoreLocator;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;
use Digidirect\CollectAbstractEntity\Helper\Places;
use Digidirect\Collect\Helper\Data;
use Digidirect\Collect\Api\CollectPlaceRepositoryInterface;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;

class AddExtraInfoToStoreLocatorItems implements ObserverInterface
{
    protected $checkoutSession;
    protected $placesHelper;
    protected $collectHelper;
    protected $logger;
    protected $_cart;
    protected $_product;
    protected $getSourceItemsBySku;

    public function __construct(
        Session $checkoutSession,
        Places $placesHelper,
        Data $collectHelper,
        GetSourceItemsBySku $getSourceItemsBySku,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\Product $product
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->placesHelper = $placesHelper;
        $this->collectHelper = $collectHelper;
        $this->getSourceItemsBySku = $getSourceItemsBySku;
        $this->logger = $logger;
        $this->_cart = $cart;
        $this->_product = $product;
    }

    public function execute(Observer $observer)
    {
        $transportObject = $observer->getTransportObject();
        $locatorStores = $transportObject->getData('items');
        $locatorStores = $this->addAvailabilityInfoToItems($locatorStores);
        $transportObject->setData(['items' => $locatorStores]);
        echo $this->console_log($locatorStores);
    }

    public function addAvailabilityInfoToItems($items)
    {
        $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $skuQty = $this->collectHelper->getSkuToQtyByItems($quoteItems);
        $places = $this->placesHelper->getAllCollectPlacesEntities($skuQty);

        $cartItems = $this->_cart->getQuote()->getAllItems();

        $stores = [];
        $totalCann = 0.0;
        $totalQtyOnOtherSources = 0;

        foreach ($items as $key => $storeData) {

            $id = $storeData['entity_id'];
            $qty = 0;

            if (empty($places[$id])) {
                continue;
            }

            $items[$key]['available'] = true;

            $sydnQty = 1;
            $bondQty = 1;
            $melbQty = 1;
            $brisQty = 1;
            $miraQty = 1;
            $cannQty = 1;
            $parrQty = 1;
            $stPetersQty = 1;
            $strathfieldQty = 1;

            foreach ($cartItems as $cartItem) {

                $prodId = $cartItem->getProductId();
                $product = $this->_product->load($prodId);

                $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());

                foreach ($sourceItems as $sourceItemId => $sourceItem) {

                    //$this->logger->info('getSourceCode:' . $sourceItem->getSourceCode() . ', getQuantity:' . $sourceItem->getQuantity());

                    $getQty = $sourceItem->getQuantity();
                    $store = $sourceItem->getSourceCode();

                    if (!in_array($store, $stores)) {
                        $stores[] = $store;
                    }

                    //$this->logger->info('stores:', ['store' => $stores]);

                    if ($id == 1 && $store == 'SYDN') {
                        $sydnQty *= $getQty;
                        $totalQtyOnOtherSources += $sydnQty;

                    } elseif ($id == 31 && $store == 'BOND') {
                        $bondQty *= $getQty;
                        $totalQtyOnOtherSources += $bondQty;

                    } elseif ($id == 7 && $store == 'MELB') {
                        $melbQty *= $getQty;
                        $totalQtyOnOtherSources += $melbQty;

                    } elseif ($id == 10 && $store == 'BRIS') {
                        $brisQty *= $getQty;
                        $totalQtyOnOtherSources += $brisQty;

                    } elseif ($id == 13 && $store == 'MIRA') {
                        $miraQty *= $getQty;
                        $totalQtyOnOtherSources += $miraQty;

                    } elseif ($id == 32 && $store == 'PARR') {
                        $parrQty *= $getQty;
                        $totalQtyOnOtherSources += $parrQty;

                    } elseif (($id == 42 || $id == 41) && $store == '3WHS') {
                        $strathfieldQty *= $getQty;
                        $totalQtyOnOtherSources += $strathfieldQty;

                    } elseif ($id == 16 && $store == 'CANN') {

                        $wiserPrice = $product->getWiserPrice();
                        $finalPrice = $product->getFinalPrice();

                        $lastPrice = min($finalPrice, $wiserPrice);

                        $totalCann += $lastPrice;
                        $cannQty *= $getQty;

//                        $this->logger->info('$wiserPrice, ' . $wiserPrice);
//                        $this->logger->info('$finalPrice, ' . $finalPrice);
//                        $this->logger->info('$totalCann, ' . $totalCann);
                    }
                }
            }

            if ($totalCann < 1000 && $cannQty > 0 && $totalQtyOnOtherSources < 1) {
                if ($id == 16) {
                    $items[$key]['click_and_collect'] = true;
                } else {
                    $items[$key]['click_and_collect'] = null;
                }
            } else {

                if ($id == 1 && $sydnQty > 0) {
                    $items[$key]['click_and_collect'] = in_array('SYDN', $stores);

                } elseif ($id == 31 && $bondQty > 0) {
                    $items[$key]['click_and_collect'] = in_array('BOND', $stores);

                } elseif ($id == 7 && $melbQty > 0) {
                    $items[$key]['click_and_collect'] = in_array('MELB', $stores);

                } elseif ($id == 10 && $brisQty > 0) {
                    $items[$key]['click_and_collect'] = in_array('BRIS', $stores);

                } elseif ($id == 13 && $miraQty > 0) {
                    $items[$key]['click_and_collect'] = in_array('MIRA', $stores);

                } elseif ($id == 16) {
                    if (in_array('CANN', $stores) && ($cannQty > 0)) {
                        $items[$key]['click_and_collect'] = true;
                    } else {
                        $items[$key]['click_and_collect'] = ($totalCann < 1000) ? null : false;
                    }

                } elseif ($id == 35) { // SWHS
                    $items[$key]['click_and_collect'] = null;

                } elseif ($id == 32 && $parrQty > 0) {
                    $items[$key]['click_and_collect'] = in_array('PARR', $stores);

                } elseif (($id == 42 || $id == 41) && $strathfieldQty > 0) {
                    // FINAL MERGE-FIXED 3WHS RULE
                    $items[$key]['click_and_collect'] = in_array('3WHS', $stores);

                } else {
                    $items[$key]['click_and_collect'] = false;
                }
            }
        }

//        $this->logger->info('$totalCann: ' . $totalCann);
//        $this->logger->info('$cannQty: ' . $cannQty);
//        $this->logger->info('$totalQtyOnOtherSources: ' . $totalQtyOnOtherSources);
//        $this->logger->info('stores:', ['items' => $items]);
        return $items;
    }

    function console_log($output, $with_script_tags = true)
    {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }
    //redeploy
}
