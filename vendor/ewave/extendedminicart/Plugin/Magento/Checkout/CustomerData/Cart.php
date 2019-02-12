<?php

namespace Ewave\ExtendedMiniCart\Plugin\Magento\Checkout\CustomerData;

class Cart
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @var \Magento\Quote\Model\Quote|null
     */
    protected $quote = null;

    /**
     * Cart constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->checkoutHelper = $checkoutHelper;
    }

    /**
     * Add tax data to result
     *
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        $items = $this->getQuote()->getAllVisibleItems();
        if (is_array($result['items'])) {
            foreach ($result['items'] as $key => $itemAsArray) {
                if ($item = $this->findItemById($itemAsArray['item_id'], $items)) {
                    $result['items'][$key][DefaultItem::PARAM_PRODUCT_TOTAL_PRICE] = $this->checkoutHelper->formatPrice(
                        (int)$item->getQty() * $item->getPriceInclTax()
                    );
                }
            }
        }
        return $result;
    }

    protected function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Find item by id in items haystack
     *
     * @param int $id
     * @param array $itemsHaystack
     * @return \Magento\Quote\Model\Quote\Item | bool
     */
    protected function findItemById($id, $itemsHaystack)
    {
        if (is_array($itemsHaystack)) {
            foreach ($itemsHaystack as $item) {
                /** @var $item \Magento\Quote\Model\Quote\Item */
                if ((int)$item->getItemId() == $id) {
                    return $item;
                }
            }
        }
        return false;
    }
}
