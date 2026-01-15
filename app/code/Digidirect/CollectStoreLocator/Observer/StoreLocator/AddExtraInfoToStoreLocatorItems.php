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
        41 => '3WHS',  // Staging
        42 => '3WHS',  // Production (old)
        44 => '3WHS',  // Production (current)
        45 => '3WHS',  // Production (additional)
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
        
        $this->logger->info('=== Store Locator Processing Started - CODE VERSION 4.0 ===');
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
        
        // Check if items are ONLY available in CANN
        $cannOnly = $inventoryData['other_sources_qty'] < 1 && in_array('CANN', $inventoryData['sources']);
        $cannTotal = $inventoryData['cann_total'];
        
        $this->logger->info('Processing ' . count($items) . ' store locations');
        $this->logger->info('CANN Only: ' . ($cannOnly ? 'YES' : 'NO') . ', CANN Total: ' . $cannTotal);
        
        foreach ($items as $key => $storeData) {
            $id = $storeData['entity_id'];
            
            $this->logger->info("Processing store entity_id: {$id}");

            if (empty($places[$id])) {
                $this->logger->info("Store {$id} not found in places, skipping");
                continue;
            }

            // All stores are selectable per business requirement
            $items[$key]['available'] = true;
            $items[$key]['click_and_collect'] = $this->determineClickAndCollect(
                $id,
                $inventoryData,
                $cannOnly
            );
            
            $this->logger->info("Store {$id} click_and_collect set to: " . 
                               ($items[$key]['click_and_collect'] === null ? 'NULL' : 
                               ($items[$key]['click_and_collect'] ? 'TRUE' : 'FALSE')));
        }

        $this->logInventoryStats($inventoryData);

        return $items;
    }

    /**
     * Calculate inventory quantities and pricing for all cart items
     * Uses multiplication logic to check if ALL products in cart are available at each source
     *
     * @param array $cartItems
     * @return array
     */
    private function calculateInventoryData($cartItems)
    {
        $storeQuantities = [];
        $availableSources = [];
        $totalCannAmount = 0.0;

        foreach ($cartItems as $cartItem) {
            try {
                $product = $this->productRepository->getById($cartItem->getProductId());
                $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());

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

                    // Calculate CANN pricing (total cart value for CANN items)
                    if ($sourceCode === 'CANN' && $quantity > 0) {
                        $totalCannAmount += $this->getEffectivePrice($product);
                    }
                }
            } catch (\Exception $e) {
                $this->logger->error('Error loading product: ' . $e->getMessage());
            }
        }

        return [
            'quantities' => $storeQuantities,
            'sources' => $availableSources,
            'cann_total' => $totalCannAmount,
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
     * Returns the sum of all positive quantities (not multiplied product)
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
     * Determine if click and collect is available for a store
     *
     * @param int $storeId
     * @param array $inventoryData
     * @param bool $cannOnly
     * @return bool|null
     */
    private function determineClickAndCollect($storeId, $inventoryData, $cannOnly)
    {
        $quantities = $inventoryData['quantities'];
        $sources = $inventoryData['sources'];
        $cannTotal = $inventoryData['cann_total'];
        $otherSourcesQty = $inventoryData['other_sources_qty'];

        $this->logger->info("=== DETERMINE CLICK AND COLLECT START ===");
        $this->logger->info("StoreId: {$storeId}, Type: " . gettype($storeId));
        $this->logger->info("CANN_STORE_ID: " . self::CANN_STORE_ID . ", Type: " . gettype(self::CANN_STORE_ID));
        $this->logger->info("Match (===): " . (($storeId === self::CANN_STORE_ID) ? 'TRUE' : 'FALSE'));
        $this->logger->info("Match (==): " . (($storeId == self::CANN_STORE_ID) ? 'TRUE' : 'FALSE'));
        $this->logger->info("CANN Only: " . ($cannOnly ? 'YES' : 'NO'));
        $this->logger->info("CANN Total: {$cannTotal}");
        $this->logger->info("CANN Minimum: " . self::CANN_MINIMUM_AMOUNT);
        $this->logger->info("=== END DIAGNOSTICS ===");

        // NEW LOGIC: If items are ONLY available in CANN
        if ($cannOnly) {
            if ($storeId === self::CANN_STORE_ID) {
                // CANN store always returns TRUE when items are CANN-only (regardless of total)
                $this->logger->info("CANN Only: Returning TRUE for CANN store (total: {$cannTotal})");
                return true;
            } else {
                // Other stores depend on CANN total
                if ($cannTotal < self::CANN_MINIMUM_AMOUNT) {
                    // Below minimum: other stores = NULL
                    $this->logger->info("CANN Only + Below Minimum ({$cannTotal}): Returning NULL for non-CANN store {$storeId}");
                    return null;
                } else {
                    // At or above minimum: other stores = FALSE
                    $this->logger->info("CANN Only + Above Minimum ({$cannTotal}): Returning FALSE for non-CANN store {$storeId}");
                    return false;
                }
            }
        }

        // Special handling for SWHS - always null
        if ($storeId === self::SWHS_STORE_ID) {
            $this->logger->info("Store ID: {$storeId} (SWHS) - Returning NULL");
            return null;
        }

        // NEW LOGIC: If products available in other stores AND CANN total < 1000, CANN = NULL
        if ($storeId === self::CANN_STORE_ID && !$cannOnly) {
            $this->logger->info("CANN store check: cannOnly={$cannOnly}, cannTotal={$cannTotal}, minimum=" . self::CANN_MINIMUM_AMOUNT);
            
            if ($cannTotal < self::CANN_MINIMUM_AMOUNT) {
                $this->logger->info("Products in other stores + CANN total < 1000: Returning NULL for CANN store");
                return null;
            } else {
                $this->logger->info("Products in other stores + CANN total >= 1000: Continuing to CANN logic");
                // Continue to CANN-specific logic below
            }
        }

        // Special handling for CANN store with minimum order (when not CANN-only and meets minimum)
        if ($storeId === self::CANN_STORE_ID) {
            $this->logger->info("!!!! CANN SPECIFIC LOGIC TRIGGERED for Store ID: {$storeId} !!!!");
            return $this->determineCannClickAndCollect($cannTotal, $quantities, $sources, $otherSourcesQty);
        }

        // Standard store handling
        if (!isset(self::STORE_MAPPINGS[$storeId])) {
            $this->logger->warning("Store ID {$storeId} not found in mappings");
            return false;
        }

        $sourceCode = self::STORE_MAPPINGS[$storeId];
        $hasInventory = isset($quantities[$sourceCode]) && $quantities[$sourceCode] > 0;
        $sourceAvailable = in_array($sourceCode, $sources);

        $this->logger->info("Store ID: {$storeId}, Source: {$sourceCode}, HasInventory: " . ($hasInventory ? 'Yes' : 'No') . 
                           ", SourceAvailable: " . ($sourceAvailable ? 'Yes' : 'No') . 
                           ", Quantity: " . ($quantities[$sourceCode] ?? 0));

        return $hasInventory && $sourceAvailable;
    }

    /**
     * Determine click and collect availability for CANN store
     *
     * @param float $cannTotal
     * @param array $quantities
     * @param array $sources
     * @param int $otherSourcesQty
     * @return bool|null
     */
    private function determineCannClickAndCollect($cannTotal, $quantities, $sources, $otherSourcesQty)
    {
        $cannQty = $quantities['CANN'] ?? 0;
        $hasCannInventory = $cannQty > 0;
        $cannAvailable = in_array('CANN', $sources);

        $this->logger->info("CANN Decision - Total: {$cannTotal}, Min: " . self::CANN_MINIMUM_AMOUNT . 
                           ", HasInventory: " . ($hasCannInventory ? 'Yes' : 'No') . 
                           ", Available: " . ($cannAvailable ? 'Yes' : 'No') .
                           ", OtherSourcesQty: {$otherSourcesQty}");

        // If cart total is below minimum for CANN-only products, return NULL
        if ($cannTotal < self::CANN_MINIMUM_AMOUNT) {
            $this->logger->info("CANN: Returning NULL - below minimum");
            return null;
        }

        // If CANN has inventory and is available and meets minimum, return true
        if ($hasCannInventory && $cannAvailable) {
            $this->logger->info("CANN: Returning TRUE - has inventory and available");
            return true;
        }

        $this->logger->info("CANN: Returning FALSE - default case");
        return false;
    }

    /**
     * Log inventory statistics for debugging
     *
     * @param array $inventoryData
     * @return void
     */
    private function logInventoryStats($inventoryData)
    {
        $this->logger->info('CANN Total Amount: ' . $inventoryData['cann_total']);
        $this->logger->info('CANN Quantity: ' . ($inventoryData['quantities']['CANN'] ?? 0));
        $this->logger->info('Other Sources Quantity: ' . $inventoryData['other_sources_qty']);
        $this->logger->info('Available Sources: ' . implode(', ', $inventoryData['sources']));
        $this->logger->info('Store Quantities: ' . json_encode($inventoryData['quantities']));
    }
}