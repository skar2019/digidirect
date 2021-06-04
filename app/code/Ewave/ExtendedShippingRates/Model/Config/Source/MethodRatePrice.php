<?php
namespace Ewave\ExtendedShippingRates\Model\Config\Source;

use Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate as Rate;

class MethodRatePrice implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Rate::PRICE_CALCULATION_OVERWRITE, 'label' => __('Overwrite')],
            ['value' => Rate::PRICE_CALCULATION_SUM, 'label' => __('Sum')],
        ];
    }
}
