<?php
namespace Digidirect\Collect\Model;

use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote\Item;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractApplyShippingVariation
 * @package Digidirect\Collect\Model
 */
class AbstractApplyShippingVariation
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbstractApplyShippingVariation constructor.
     * @param Session $session
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $session,
        LoggerInterface $logger
    ) {
        $this->checkoutSession = $session;
        $this->logger = $logger;
    }

    /**
     * @param string|null $collectPlaceId
     * @param string|null $storageName
     * @return bool|Item[]
     */
    public function applyCollectParamsToAllItems($collectPlaceId = null, $storageName = null)
    {
        // $collectPlaceId and $storageName must be both NULL or both string.
        if ((empty($collectPlaceId) && !empty($storageName))
            || (!empty($collectPlaceId) && empty($storageName))) {
            return false;
        }

        $this->logger->info('===== applyCollectParamsToAllItems START =====');
        $this->logger->info('collectPlaceId: ' . var_export($collectPlaceId, true));
        $this->logger->info('storageName: ' . var_export($storageName, true));

        $this->checkoutSession->setCollectPlaceId($collectPlaceId);
        $this->checkoutSession->setCollectPlaceStorageName($storageName);

        $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
        /** @var Item $quoteItem */
        foreach ($quoteItems as $quoteItem) {
            $quoteItem->setCollectPlaceId($collectPlaceId);
            $quoteItem->setCollectPlaceStorageName($storageName);
        }

        // Clear shipping method when switching to delivery mode (collectPlaceId is null)
        if ($collectPlaceId === null) {
            $shippingAddress = $this->checkoutSession->getQuote()->getShippingAddress();

            $this->logger->info('CLEARING SHIPPING METHOD (delivery mode)');
            $this->logger->info('Before clear: ' . var_export($shippingAddress->getShippingMethod(), true));

            $shippingAddress->setShippingMethod(null);
            $shippingAddress->setCollectShippingRates(true);

            $this->logger->info('After clear (before collectTotals): ' . var_export($shippingAddress->getShippingMethod(), true));
        }

        try {
            $this->logger->info('BEFORE collectTotals - shipping method: ' . var_export($this->checkoutSession->getQuote()->getShippingAddress()->getShippingMethod(), true));

            $this->checkoutSession->getQuote()->collectTotals();

            $this->logger->info('AFTER collectTotals - shipping method: ' . var_export($this->checkoutSession->getQuote()->getShippingAddress()->getShippingMethod(), true));

            $this->checkoutSession->getQuote()->save();

            $this->logger->info('AFTER save - shipping method: ' . var_export($this->checkoutSession->getQuote()->getShippingAddress()->getShippingMethod(), true));
            $this->logger->info('===== applyCollectParamsToAllItems END =====');
        } catch (\Exception $e) {
            $this->logger->error('Exception in applyCollectParamsToAllItems: ' . $e->getMessage());
            return false;
        }
        return $quoteItems;
    }
}
