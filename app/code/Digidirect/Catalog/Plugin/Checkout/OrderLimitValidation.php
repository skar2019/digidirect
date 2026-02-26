<?php
namespace Digidirect\Catalog\Plugin\Checkout;

use Magento\Checkout\Model\Cart;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class OrderLimitValidation
{
    const DEFAULT_ORDER_LIMIT = 10;
    const GLOBAL_CART_LIMIT   = 10;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    public function __construct(ManagerInterface $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    /**
     * Validate per-product order limit AND global cart limit before adding to cart.
     *
     * @param Cart $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param mixed $request
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct(Cart $subject, $product, $request = null)
    {
        $requestedQty = $this->resolveRequestedQty($request);
        $quote        = $subject->getQuote();

        // --- 1. Per-product limit check ---
        $orderLimit = (int)$product->getData('order_limit');
        if (empty($orderLimit) || $orderLimit <= 0) {
            $orderLimit = self::DEFAULT_ORDER_LIMIT;
        }

        $qtyInCartForProduct = 0;
        foreach ($quote->getAllItems() as $item) {
            if ((int)$item->getProductId() === (int)$product->getId()
                && $item->getParentItem() === null
            ) {
                $qtyInCartForProduct += (int)$item->getQty();
            }
        }

        $totalForProduct = $qtyInCartForProduct + $requestedQty;

        if ($totalForProduct > $orderLimit) {
            $remainingForProduct = max(0, $orderLimit - $qtyInCartForProduct);
            if ($remainingForProduct <= 0) {
                throw new LocalizedException(__(
                    'You have reached the maximum order limit of %1 for "%2". '
                    . 'You already have %3 in your cart.',
                    $orderLimit,
                    $product->getName(),
                    $qtyInCartForProduct
                ));
            }
            throw new LocalizedException(__(
                'Cannot add %1 item(s) of "%2". '
                . 'You already have %3 in your cart and the per-product limit is %4. '
                . 'You can add up to %5 more.',
                $requestedQty,
                $product->getName(),
                $qtyInCartForProduct,
                $orderLimit,
                $remainingForProduct
            ));
        }

        // --- 2. Global cart total limit check ---
        $totalCartQty = 0;
        foreach ($quote->getAllItems() as $item) {
            if ($item->getParentItem() === null) {
                $totalCartQty += (int)$item->getQty();
            }
        }

        $totalCartAfterAdd = $totalCartQty + $requestedQty;

        if ($totalCartAfterAdd > self::GLOBAL_CART_LIMIT) {
            $remainingGlobal = max(0, self::GLOBAL_CART_LIMIT - $totalCartQty);
            if ($remainingGlobal <= 0) {
                throw new LocalizedException(__(
                    'Your cart has reached the maximum allowed quantity of %1 items. '
                    . 'Please remove some items before adding more.',
                    self::GLOBAL_CART_LIMIT
                ));
            }
            throw new LocalizedException(__(
                'Cannot add %1 item(s) to your cart. '
                . 'Your cart currently has %2 item(s) and the maximum total allowed is %3. '
                . 'You can add up to %4 more item(s).',
                $requestedQty,
                $totalCartQty,
                self::GLOBAL_CART_LIMIT,
                $remainingGlobal
            ));
        }

        return [$product, $request];
    }

    /**
     * Resolve requested qty from various request formats.
     *
     * @param mixed $request
     * @return int
     */
    private function resolveRequestedQty($request): int
    {
        if (is_numeric($request)) {
            return max(1, (int)$request);
        }
        if ($request instanceof \Magento\Framework\DataObject) {
            return max(1, (int)$request->getQty());
        }
        if (is_array($request) && isset($request['qty'])) {
            return max(1, (int)$request['qty']);
        }
        return 1;
    }
}