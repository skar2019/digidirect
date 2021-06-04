<?php
namespace Ewave\ProductPriority\Model\Config\Source;

/**
 * Class PeriodForNewProducts
 * @package Ewave\ProductPriority\Model\Config\Source
 */
class PeriodForNewProducts implements \Magento\Framework\Option\ArrayInterface
{
    const ONE_WEEK      = 7;
    const TWO_WEEK      = 14;
    const THREE_WEEK    = 21;
    const ONE_MONTH     = 30;
    const TWO_MONTH     = 60;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ONE_WEEK,     'label' => __('1 Week')],
            ['value' => self::TWO_WEEK,     'label' => __('2 Weeks')],
            ['value' => self::THREE_WEEK,   'label' => __('3 Weeks')],
            ['value' => self::ONE_MONTH,    'label' => __('1 Month')],
            ['value' => self::TWO_MONTH,    'label' => __('2 Months')]
        ];
    }
}
