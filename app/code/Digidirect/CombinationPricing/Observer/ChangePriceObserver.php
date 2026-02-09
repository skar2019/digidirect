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
            // Check if feature is enabled
            if (!$this->helper->isEnabled()) {
                return;
            }

            $quote = $observer->getQuote();
            $storeId = $quote->getStoreId();

            // Get active combinations
            $activeCombinations = $this->helper->getActiveCombinations($storeId);

            if (empty($activeCombinations)) {
                return;
            }

            // Get all quote items indexed by SKU
            $quoteItems = [];
            foreach ($quote->getAllVisibleItems() as $item) {
                $sku = $item->getProduct()->getSku();
                $quoteItems[$sku] = $item;
            }

            // Process each combination
            foreach ($activeCombinations as $combination) {
                $firstSku = $combination['first_sku'];
                $secondSku = $combination['second_sku'];
                $fixedPrice = $combination['fixed_price'];

                // Check if both products exist in cart
                if (isset($quoteItems[$firstSku]) && isset($quoteItems[$secondSku])) {
                    $secondProductItem = $quoteItems[$secondSku];
                    
                    // Apply custom price
                    $secondProductItem->setCustomPrice($fixedPrice);
                    $secondProductItem->setOriginalCustomPrice($fixedPrice);
                    $secondProductItem->getProduct()->setIsSuperMode(true);
                    
                    $this->logger->info(
                        sprintf(
                            'Digidirect_CombinationPricing: Applied fixed price %s to product %s when combined with %s (Quote ID: %s)',
                            $fixedPrice,
                            $secondSku,
                            $firstSku,
                            $quote->getId()
                        )
                    );
                }
            }
        } catch (\Exception $e) {
            $this->logger->error(
                'Digidirect_CombinationPricing Error: ' . $e->getMessage()
            );
        }
    }
}