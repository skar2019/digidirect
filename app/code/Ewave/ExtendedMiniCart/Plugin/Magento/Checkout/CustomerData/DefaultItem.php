<?php

namespace Ewave\ExtendedMiniCart\Plugin\Magento\Checkout\CustomerData;

/**
 * Class DefaultItem
 *
 * @package Ewave\ExtendedMiniCart\Plugin\Magento\Checkout\CustomerData
 */
class DefaultItem
{
    const PARAM_PRODUCT_TOTAL_PRICE = 'product_total_price';
    const PARAM_PRODUCT_TOTAL_PRICE_VALUE = 'product_total_price_value';

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @var \Ewave\ExtendedMiniCart\Helper\Data
     */
    protected $minicartHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DefaultItem constructor.
     *
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Ewave\ExtendedMiniCart\Helper\Data $minicartHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Ewave\ExtendedMiniCart\Helper\Data $minicartHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->checkoutHelper = $checkoutHelper;
        $this->minicartHelper = $minicartHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Checkout\CustomerData\DefaultItem $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetItemData(
        \Magento\Checkout\CustomerData\DefaultItem $subject,
        array $result
    ) {
        if ($this->minicartHelper->isShowLineItemSubtotal($this->getStoreId())) {
            $total = $result['product_price_value'] * $result['qty'];
            $result[self::PARAM_PRODUCT_TOTAL_PRICE] = $this->checkoutHelper->formatPrice($total);
            $result[self::PARAM_PRODUCT_TOTAL_PRICE_VALUE] = $total;
        }

        return $result;
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
