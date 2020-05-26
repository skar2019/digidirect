<?php

namespace Ewave\ShippingAvailabilityCheck\Api;

/**
 * Interface CheckManagementInterface
 * @package Ewave\ShippingAvailabilityCheck\Api
 */
interface CheckManagementInterface
{
    /**
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param \Ewave\ShippingAvailabilityCheck\Api\Data\ProductDataInterface $productData
     * @param int|null $customerId
     * @return \Ewave\ShippingAvailabilityCheck\Api\Data\ShippingMethodInterface[]
     */
    public function getShippingMethodList($address, $productData, $customerId = null);

    /**
     * @param \Ewave\ShippingAvailabilityCheck\Api\Data\ProductDataInterface[] $productData
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int|null $customerId
     * @return array
     */
    public function getShippingMethodListForMultipleProducts($productData, $address, $customerId = null);

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @return mixed
     */
    public function estimateByAddress($quote, \Magento\Quote\Api\Data\EstimateAddressInterface $address);
}
