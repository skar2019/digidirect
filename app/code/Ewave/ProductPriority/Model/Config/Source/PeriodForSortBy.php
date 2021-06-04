<?php
namespace Ewave\ProductPriority\Model\Config\Source;

/**
 * Class PeriodForSortBy
 * @package Ewave\ProductPriority\Model\Config\Source
 */
class PeriodForSortBy implements \Magento\Framework\Option\ArrayInterface
{
    const ONE_MONTH     = 30;
    const THREE_MONTH   = 90;
    const SIX_MONTH     = 180;
    const ONE_YEAR      = 365;
    const THREE_YEAR    = 1095;
    const FIVE_YEAR     = 1825;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ONE_MONTH,    'label' => __('1 Month')],
            ['value' => self::THREE_MONTH,  'label' => __('3 Months')],
            ['value' => self::SIX_MONTH,    'label' => __('6 Months')],
            ['value' => self::ONE_YEAR,     'label' => __('1 Year')],
            ['value' => self::THREE_YEAR,   'label' => __('3 Years')],
            ['value' => self::FIVE_YEAR,    'label' => __('5 Years')]
        ];
    }
}
