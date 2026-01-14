<?php
namespace Digidirect\CollectStoreLocator\Observer\StoreLocator;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;
use Digidirect\CollectAbstractEntity\Helper\Places;
use Digidirect\Collect\Helper\Data;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class AddExtraInfoToStoreLocatorItems
 * @package Digidirect\CollectStoreLocator\Observer\StoreLocator
 */
class AddExtraInfoToStoreLocatorItems implements ObserverInterface
{
    private const STORE_MAPPINGS = [
        1 => 'SYDN',
        31 => 'BOND',
        7 => 'MELB',
        10 => 'BRIS',
        13 => 'MIRA',
        16 => 'CANN',
        32 => 'PARR',
        35 => 'SWHS',
        41 => '3WHS',
        42 => '3WHS',
        44 => '3WHS',
        45 => '3WHS',
    ];

    private const CANN_MINIMUM_AMOUNT = 1000;
    private const CANN_STORE_ID = 16;
    private const SWHS_STORE_ID = 35;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var Places
     */
    private $placesHelper;

    /**
     * @var Data
     */
    private $collectHelper;

    /**
     * @var GetSourceItemsBySku
     */
    private $getSourceItemsBySku;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * AddExtraInfoToStoreLocatorItems constructor.
     */
    public function __construct(
        Session $checkoutSession,
        Places $placesHelper,
        Data $collectHelper,
        GetSourceItemsBySku $getSourceItemsBySku,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->placesHelper = $placesHelper;
        $this->collectHelper = $collectHelper;
        $this->getSourceItemsBySku = $getSourceItemsBySku;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $transportObject = $observer->getTransportObject();
        $locatorStores = $transportObject->getData('items');
        
        $this->logger->info('=== Store Locator Processing Started ===');
        $this->logger->info('Total stores to process: ' . count($locatorStores));
        
        $locatorStores = $this->addAvailabilityInfoToItems($locatorStores);
        $transportObject->setData(['items' => $locatorStores]);
        
        $this->logger->info('=== Store Locator Processing Completed ===');
    }

    /**
     * @param array $items
     * @return array
     */
    private function addAvailabilityInfoToItems($items)
    {
        $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $skuQty = $this->collectHelper->getSkuToQtyByItems($quoteItems);
        $places = $this->placesHelper->getAllCollectPlacesEntities($skuQty);

        // Pre-calculate inventory and pricing data
        $inventoryData = $this->calculateInventoryData($quoteItems);
        
        $this->logger->info('Processing ' . count($items) . ' store locations');
        
        // Check if we're in the special "below minimum with CANN only" scenario
        $isSpecialCase = $this->isSpecialCannOnlyCase($inventoryData);
        
        foreach ($items as $key => $storeData) {
            $id = $storeData['entity_id'];
            
            $this->logger->info("Processing store entity_id: {$id}");

            if (empty($places[$id])) {
                $this->logger->info("Store {$id} not found in places, skipping");
                continue;
            }

            // All stores are selectable per business requirement
            $items[$key]['available'] = true;
            
            // Determine click and collect based on scenario
            if ($isSpecialCase) {
                $items[$key]['click_and_collect'] = $this->determineClickAndCollectSpecialCase($id, $inventoryData);
            } else {
                $items[$key]['click_and_collect'] = $this->determineClickAndCollectNormal($id, $inventoryData);
            }
            
            $this->logger->info("Store {$id} click_and_collect set to: " . 
                               ($items[$key]['click_and_collect'] === null ? 'NULL' : 
                               ($items[$key]['click_and_collect'] ? 'TRUE' : 'FALSE')));
        }

        $this->logInventoryStats($inventoryData);

        return $items;
    }

    /**
     * Calculate inventory quantities and pricing for all cart items
     *
     * @param array $cartItems
     * @return array
     */
    private function calculateInventoryData($cartItems)
    {
        $storeQuantities = [];
        $availableSources = [];
        $totalCartAmount = 0.0;

        foreach ($cartItems as $cartItem) {
            try {
                $product = $this->productRepository->getById($cartItem->getProductId());
                $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());

                // Calculate total cart value
                $itemQty = $cartItem->getQty();
                $totalCartAmount += ($this->getEffectivePrice($product) * $itemQty);

                foreach ($sourceItems as $sourceItem) {
                    $sourceCode = $sourceItem->getSourceCode();
                    $quantity = $sourceItem->getQuantity();

                    // Track available sources (has ANY inventory for ANY product)
                    if ($quantity > 0 && !in_array($sourceCode, $availableSources)) {
                        $availableSources[] = $sourceCode;
                    }

                    // Calculate quantities by source using multiplication
                    // This ensures ALL products must have stock (0 in any product = 0 total)
                    if (!isset($storeQuantities[$sourceCode])) {
                        $storeQuantities[$sourceCode] = 1;
                    }
                    $storeQuantities[$sourceCode] *= $quantity;
                }
            } catch (\Exception $e) {
                $this->logger->error('Error loading product: ' . $e->getMessage());
            }
        }

        return [
            'quantities' => $storeQuantities,
            'sources' => $availableSources,
            'cart_total' => $totalCartAmount,
            'other_sources_qty' => $this->calculateOtherSourcesQuantity($storeQuantities)
        ];
    }

    /**
     * Get the effective price (lowest of final price or wiser price)
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return float
     */
    private function getEffectivePrice($product)
    {
        $finalPrice = (float)$product->getFinalPrice();
        $wiserPrice = (float)$product->getData('wiser_price');

        return ($wiserPrice > 0 && $wiserPrice < $finalPrice) ? $wiserPrice : $finalPrice;
    }

    /**
     * Calculate total quantity in non-CANN sources
     *
     * @param array $storeQuantities
     * @return int
     */
    private function calculateOtherSourcesQuantity($storeQuantities)
    {
        $total = 0;
        foreach ($storeQuantities as $source => $qty) {
            if ($source !== 'CANN' && $qty > 0) {
                $total += $qty;
            }
        }
        return $total;
    }

    /**
     * Check if we're in the special case: below minimum, CANN has inventory, no other sources
     *
     * @param array $inventoryData
     * @return bool
     */
    private function isSpecialCannOnlyCase($inventoryData)
    {
        $cartTotal = $inventoryData['cart_total'];
        $cannQty = $inventoryData['quantities']['CANN'] ?? 0;
        $otherSourcesQty = $inventoryData['other_sources_qty'];

        return ($cartTotal < self::CANN_MINIMUM_AMOUNT && $cannQty > 0 && $otherSourcesQty < 1);
    }

    /**
     * Determine click and collect for special case (below minimum, CANN only)
     *
     * @param int $storeId
     * @param array $inventoryData
     * @return bool|null
     */
    private function determineClickAndCollectSpecialCase($storeId, $inventoryData)
    {
        // In special case: CANN = true, all others = NULL
        if ($storeId === self::CANN_STORE_ID) {
            return true;
        }
        return null;
    }

    /**
     * Determine click and collect for normal case
     *
     * @param int $storeId
     * @param array $inventoryData
     * @return bool|null
     */
    private function determineClickAndCollectNormal($storeId, $inventoryData)
    {
        $quantities = $inventoryData['quantities'];
        $sources = $inventoryData['sources'];
        $cartTotal = $inventoryData['cart_total'];

        // Special handling for SWHS - always null
        if ($storeId === self::SWHS_STORE_ID) {
            $this->logger->info("Store ID: {$storeId} (SWHS) - returning NULL");
            return null;
        }

        // Special handling for CANN store
        if ($storeId === self::CANN_STORE_ID) {
            return $this->determineCannClickAndCollect($cartTotal, $quantities, $sources);
        }

        // Standard store handling
        if (!isset(self::STORE_MAPPINGS[$storeId])) {
            $this->logger->warning("Store ID {$storeId} not found in mappings");
            return false;
        }

        $sourceCode = self::STORE_MAPPINGS[$storeId];
        $hasInventory = isset($quantities[$sourceCode]) && $quantities[$sourceCode] > 0;
        $sourceAvailable = in_array($sourceCode, $sources);

        $this->logger->info("Store ID: {$storeId}, Source: {$sourceCode}, HasInventory: " . 
                           ($hasInventory ? 'Yes' : 'No') . ", SourceAvailable: " . 
                           ($sourceAvailable ? 'Yes' : 'No') . ", Quantity: " . 
                           ($quantities[$sourceCode] ?? 0));

        return $hasInventory && $sourceAvailable;
    }

    /**
     * Determine click and collect availability for CANN store in normal case
     *
     * @param float $cartTotal
     * @param array $quantities
     * @param array $sources
     * @return bool|null
     */
    private function determineCannClickAndCollect($cartTotal, $quantities, $sources)
    {
        $cannQty = $quantities['CANN'] ?? 0;
        $hasCannInventory = $cannQty > 0;
        $cannAvailable = in_array('CANN', $sources);

        $this->logger->info("CANN Store - Cart Total: {$cartTotal}, Has Inventory: " . 
                           ($hasCannInventory ? 'Yes' : 'No') . ", Quantity: {$cannQty}");

        // NEW REQUIREMENT: If cart total >= $1000, CANN must be available
        if ($cartTotal >= self::CANN_MINIMUM_AMOUNT) {
            $this->logger->info("CANN - Cart meets minimum ($1000+), returning TRUE");
            return true;
        }

        // Below minimum - check inventory
        if ($hasCannInventory && $cannAvailable) {
            return true;
        }

        // Below minimum with no inventory - return NULL
        return null;
    }

    /**
     * Log inventory statistics for debugging
     *
     * @param array $inventoryData
     * @return void
     */
    private function logInventoryStats($inventoryData)
    {
        $this->logger->info('Cart Total Amount: ' . $inventoryData['cart_total']);
        $this->logger->info('CANN Quantity: ' . ($inventoryData['quantities']['CANN'] ?? 0));
        $this->logger->info('Other Sources Quantity: ' . $inventoryData['other_sources_qty']);
        $this->logger->info('Available Sources: ' . implode(', ', $inventoryData['sources']));
        $this->logger->info('Store Quantities: ' . json_encode($inventoryData['quantities']));
    }
}