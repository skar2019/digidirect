<?php
namespace Ewave\ExtendedShippingRates\Plugin\Magento\Checkout\Model;

use Ewave\ExtendedShippingRates\Model\Carrier\Shipping;

/**
 * Class DefaultConfigProvider
 * @package Ewave\ExtendedShippingRates\Magento\Checkout\Model
 */
class DefaultConfigProvider
{
    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    ) {
        if (!in_array(Shipping::SHIPPING, $result['activeCarriers'])) {
            array_push($result['activeCarriers'], Shipping::SHIPPING);
        }
        return $result;
    }
}
