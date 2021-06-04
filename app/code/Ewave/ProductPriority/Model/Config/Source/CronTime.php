<?php
namespace Ewave\ProductPriority\Model\Config\Source;

/**
 * Class CronTime
 * @package Ewave\ProductPriority\Model\Config\Source
 */
class CronTime implements \Magento\Framework\Option\ArrayInterface
{
    const EVERY_HOUR = '0 * * * *';
    const EVERY_DAY = '0 0 * * *';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Custom Expression')],
            ['value' => self::EVERY_DAY, 'label' => __('Every Day')],
            ['value' => self::EVERY_HOUR, 'label' => __('Every Hour')]
        ];
    }
}
