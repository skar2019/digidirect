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
            $this->logger->info('========================================');
            $this->logger->info('Digidirect_CombinationPricing Observer triggered');
            
            // Check if feature is enabled
            if (!$this->helper->isEnabled()) {
                $this->logger->info('Digidirect_CombinationPricing: Module is DISABLED');
                $this->logger->info('========================================');
                return;
            }

            $this->logger->info('Digidirect_CombinationPricing: Module is ENABLED');

            $quote = $observer->getQuote();
            $storeId = $quote->getStoreId();

            $this->logger->info('Quote ID: ' . $quote->getId());
            $this->logger->info('Store ID: ' . $storeId);

            // Get active combinations
            $activeCombinations = $this->helper->getActiveCombinations($storeId);

            if (empty($activeCombinations)) {
                $this->logger->info('Digidirect_CombinationPricing: No active combinations found');
                $this->logger->info('========================================');
                return;
            }

            $this->logger->info('Active combinations count: ' . count($activeCombinations));

            // Get all quote items indexed by SKU
            $quoteItems = [];
            $this->logger->info('Cart items:');
            foreach ($quote->getAllVisibleItems() as $item) {
                $sku = $item->getProduct()->getSku();
                $quoteItems[$sku] = $item;
                $this->logger->info('  - SKU: ' . $sku . ' | Name: ' . $item->getName() . ' | Price: ' . $item->getPrice() . ' | Custom Price: ' . ($item->getCustomPrice() ?? 'NULL'));
            }

            // Process each combination
            foreach ($activeCombinations as $index => $combination) {
                $firstSku = $combination['first_sku'];
                $secondSku = $combination['second_sku'];
                $fixedPrice = $combination['fixed_price'];

                $this->logger->info("--- Processing Combination #" . $index . " ---");
                $this->logger->info("  First SKU required: {$firstSku}");
                $this->logger->info("  Second SKU to modify: {$secondSku}");
                $this->logger->info("  Fixed price to apply: {$fixedPrice}");

                // Check if first product exists
                $hasFirstProduct = isset($quoteItems[$firstSku]);
                $this->logger->info("  First product in cart: " . ($hasFirstProduct ? 'YES' : 'NO'));

                // Check if second product exists
                $hasSecondProduct = isset($quoteItems[$secondSku]);
                $this->logger->info("  Second product in cart: " . ($hasSecondProduct ? 'YES' : 'NO'));

                // Check if both products exist in cart
                if ($hasFirstProduct && $hasSecondProduct) {
                    $secondProductItem = $quoteItems[$secondSku];
                    
                    $this->logger->info("  ✓ Both products found! Applying price...");
                    $this->logger->info("  Original price: " . $secondProductItem->getPrice());
                    $this->logger->info("  Current custom price: " . ($secondProductItem->getCustomPrice() ?? 'NULL'));
                    
                    // Apply custom price
                    $secondProductItem->setCustomPrice($fixedPrice);
                    $secondProductItem->setOriginalCustomPrice($fixedPrice);
                    $secondProductItem->getProduct()->setIsSuperMode(true);
                    
                    $this->logger->info("  New custom price set: " . $secondProductItem->getCustomPrice());
                    $this->logger->info("  SUCCESS: Applied fixed price {$fixedPrice} to product {$secondSku}");
                } else {
                    $this->logger->info("  ✗ Combination NOT matched - missing required products in cart");
                    if (!$hasFirstProduct) {
                        $this->logger->info("    Missing: {$firstSku}");
                    }
                    if (!$hasSecondProduct) {
                        $this->logger->info("    Missing: {$secondSku}");
                    }
                }
            }
            
            $this->logger->info('========================================');
        } catch (\Exception $e) {
            $this->logger->error('Digidirect_CombinationPricing Error: ' . $e->getMessage());
            $this->logger->error('Stack trace: ' . $e->getTraceAsString());
            $this->logger->info('========================================');
        }
        
    }
}