<?php
namespace Digidirect\CollectStoreLocator\Observer\StoreLocator;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;
use Digidirect\CollectAbstractEntity\Helper\Places;
use Digidirect\Collect\Helper\Data;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;

class AddExtraInfoToStoreLocatorItems implements ObserverInterface
{
    protected $checkoutSession;
    protected $placesHelper;
    protected $collectHelper;
    protected $getSourceItemsBySku;
    protected $logger;
    protected $_cart;
    protected $_product;

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
    }

    public function addAvailabilityInfoToItems(array $items)
    {
        $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $skuQty = $this->collectHelper->getSkuToQtyByItems($quoteItems);
        $places = $this->placesHelper->getAllCollectPlacesEntities($skuQty);
        $cartItems = $this->_cart->getQuote()->getAllItems();

        foreach ($items as $key => $storeData) {
            $id = $storeData['entity_id'];
            $items[$key]['available'] = true; // all stores selectable
            $items[$key]['click_and_collect'] = false; // default

            // Initialize per-store quantities
            $storeQty = [
                'SYDN' => 1,
                'BOND' => 1,
                'MELB' => 1,
                'BRIS' => 1,
                'MIRA' => 1,
                'CANN' => 1,
                'PARR' => 1,
                '3WHS' => 1 // STRATH
            ];

            $totalCann = 0.0;
            $totalQtyOnOtherSources = 0;

            foreach ($cartItems as $cartItem) {
                $product = $this->_product->load($cartItem->getProductId());
                $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());

                foreach ($sourceItems as $sourceItem) {
                    $code = $sourceItem->getSourceCode();
                    $qty = $sourceItem->getQuantity();

                    if (isset($storeQty[$code])) {
                        $storeQty[$code] *= $qty;
                        $totalQtyOnOtherSources += $storeQty[$code];
                    }

                    if ($code === 'CANN') {
                        $price = $product->getFinalPrice();
                        $wiserPrice = $product->getWiserPrice();
                        $totalCann += min($price, $wiserPrice);
                    }
                }
            }

            // Determine click_and_collect based on store ID and qty
            switch ($id) {
                case 1: // SYDN
                    $items[$key]['click_and_collect'] = $storeQty['SYDN'] > 0;
                    break;
                case 31: // BOND
                    $items[$key]['click_and_collect'] = $storeQty['BOND'] > 0;
                    break;
                case 7: // MELB
                    $items[$key]['click_and_collect'] = $storeQty['MELB'] > 0;
                    break;
                case 10: // BRIS
                    $items[$key]['click_and_collect'] = $storeQty['BRIS'] > 0;
                    break;
                case 13: // MIRA
                    $items[$key]['click_and_collect'] = $storeQty['MIRA'] > 0;
                    break;
                case 16: // CANN
                    if ($totalCann < 1000 && $storeQty['CANN'] > 0 && $totalQtyOnOtherSources < 1) {
                        $items[$key]['click_and_collect'] = true;
                    } else {
                        $items[$key]['click_and_collect'] = $storeQty['CANN'] > 0;
                    }
                    break;
                case 32: // PARR
                    $items[$key]['click_and_collect'] = $storeQty['PARR'] > 0;
                    break;
                case 41: // STRATH / 3WHS
                    $items[$key]['click_and_collect'] = $storeQty['3WHS'] > 0;
                    break;
                default:
                    $items[$key]['click_and_collect'] = false;
            }

            // Optional: log per-store results for debugging
            $this->logger->info("Store ID {$id}: click_and_collect = " . ($items[$key]['click_and_collect'] ? 'true' : 'false'));
        }

        return $items;
    }
}
