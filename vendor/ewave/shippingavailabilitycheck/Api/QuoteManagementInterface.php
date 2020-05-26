<?php

namespace Ewave\ShippingAvailabilityCheck\Api;

/**
 * Interface QuoteManagementInterface
 * @package Ewave\ShippingAvailabilityCheck\Api
 */
interface QuoteManagementInterface
{
    /**
     * @param \Ewave\ShippingAvailabilityCheck\Api\Data\ProductDataInterface $productData
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int|null $customerId
     * @return mixed
     */
    public function getExistedOrCreateNewQuote($productData, $address, $customerId = null);

    /**
     * @param \Ewave\ShippingAvailabilityCheck\Api\Data\ProductDataInterface $productData
     * @param string $hash
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int|null $customerId
     * @param \Magento\Catalog\Model\Product|null $product
     * @return mixed
     */
    public function createQuote(
        $productData,
        $hash,
        $address,
        $customerId,
        $product = null
    );
}
