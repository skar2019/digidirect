<?php
namespace Digidirect\CombinationPricing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Digidirect\CombinationPricing\Helper\Data as Helper;
use Psr\Log\LoggerInterface;

class ChangePriceObserver implements ObserverInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected static $processedQuotes = [];

    /**
     * @param Helper $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Helper $helper,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            // Check if feature is enabled
            if (!$this->helper->isEnabled()) {
                return;
            }

            $quote = $observer->getQuote();
            
            // Skip if quote is not active
            if (!$quote || !$quote->getId()) {
                return;
            }

            $storeId = $quote->getStoreId();
            $quoteId = $quote->getId();

            // Create a unique key for this quote state
            $quoteStateKey = $this->getQuoteStateKey($quote);

            // Skip if we've already processed this exact quote state in this request
            if (isset(self::$processedQuotes[$quoteStateKey])) {
                $this->logger->info('Digidirect_CombinationPricing: Already processed this quote state, skipping');
                return;
            }

            // Mark this quote state as processed
            self::$processedQuotes[$quoteStateKey] = true;

            $this->logger->info('========================================');
            $this->logger->info('Digidirect_CombinationPricing Observer triggered');
            $this->logger->info('Quote ID: ' . $quoteId);
            $this->logger->info('Store ID: ' . $storeId);

            // Get all quote items indexed by SKU
            $quoteItems = [];
            $this->logger->info('Cart items:');
            foreach ($quote->getAllVisibleItems() as $item) {
                $sku = $item->getProduct()->getSku();
                $quoteItems[$sku] = $item;
                $this->logger->info('  - SKU: ' . $sku . ' | Name: ' . $item->getName() . ' | Qty: ' . $item->getQty());
            }

            // Get active combinations
            $activeCombinations = $this->helper->getActiveCombinations($storeId);

            if (empty($activeCombinations)) {
                $this->logger->info('Digidirect_CombinationPricing: No active combinations found');
                
                // Reset ALL custom prices when no combinations are active
                $this->resetCustomPrices($quoteItems);
                
                $this->logger->info('========================================');
                return;
            }

            $this->logger->info('Active combinations count: ' . count($activeCombinations));

            // Track which SKUs should have custom prices
            $skusWithCustomPrice = [];

            // Process each combination
            foreach ($activeCombinations as $index => $combination) {
                $firstSku = $combination['first_sku'];
                $secondSku = $combination['second_sku'];
                $fixedPrice = $combination['fixed_price'];

                $this->logger->info("--- Processing Combination #" . $index . " ---");
                $this->logger->info("  First SKU required: {$firstSku}");
                $this->logger->info("  Second SKU to modify: {$secondSku}");
                $this->logger->info("  Fixed price to apply: {$fixedPrice}");

                // Check if both products exist in cart
                $hasFirstProduct = isset($quoteItems[$firstSku]);
                $hasSecondProduct = isset($quoteItems[$secondSku]);

                $this->logger->info("  First product in cart: " . ($hasFirstProduct ? 'YES' : 'NO'));
                $this->logger->info("  Second product in cart: " . ($hasSecondProduct ? 'YES' : 'NO'));

                if ($hasFirstProduct && $hasSecondProduct) {
                    $secondProductItem = $quoteItems[$secondSku];
                    
                    // Only apply if price is different from what's already set
                    $currentCustomPrice = $secondProductItem->getCustomPrice();
                    
                    if ($currentCustomPrice != $fixedPrice) {
                        $this->logger->info("  ✓ Both products found! Applying price...");
                        
                        // Apply custom price
                        $secondProductItem->setCustomPrice($fixedPrice);
                        $secondProductItem->setOriginalCustomPrice($fixedPrice);
                        $secondProductItem->getProduct()->setIsSuperMode(true);
                        
                        $this->logger->info("  SUCCESS: Applied fixed price {$fixedPrice} to product {$secondSku}");
                    } else {
                        $this->logger->info("  Price already set correctly ({$fixedPrice}), skipping");
                    }
                    
                    // Track this SKU as having a custom price
                    $skusWithCustomPrice[] = $secondSku;
                } else {
                    $this->logger->info("  ✗ Combination NOT matched - missing required products in cart");
                }
            }

            // Reset custom prices for items NOT in active combinations
            foreach ($quoteItems as $sku => $item) {
                if (!in_array($sku, $skusWithCustomPrice) && $item->getCustomPrice() !== null) {
                    $this->logger->info("Resetting custom price for SKU: {$sku} (no longer in active combination)");
                    $item->setCustomPrice(null);
                    $item->setOriginalCustomPrice(null);
                    $item->getProduct()->setIsSuperMode(false);
                }
            }
            
            $this->logger->info('========================================');
        } catch (\Exception $e) {
            $this->logger->error('Digidirect_CombinationPricing Error: ' . $e->getMessage());
            $this->logger->error('Stack trace: ' . $e->getTraceAsString());
            $this->logger->info('========================================');
        }
    }

    /**
     * Generate a unique key for the current quote state
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return string
     */
    protected function getQuoteStateKey($quote)
    {
        $items = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $items[] = $item->getProduct()->getSku() . ':' . $item->getQty();
        }
        sort($items);
        
        return $quote->getId() . '_' . md5(implode('|', $items));
    }

    /**
     * Reset custom prices for all items
     *
     * @param array $quoteItems
     * @return void
     */
    protected function resetCustomPrices($quoteItems)
    {
        foreach ($quoteItems as $item) {
            if ($item->getCustomPrice() !== null) {
                $this->logger->info('Resetting custom price for SKU: ' . $item->getProduct()->getSku());
                $item->setCustomPrice(null);
                $item->setOriginalCustomPrice(null);
                $item->getProduct()->setIsSuperMode(false);
            }
        }
    }
}