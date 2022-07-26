<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */

namespace MageSpark\Base\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class NotificationType
 *
 * @package MageSpark\Base\Model\Source
 */
class NotificationType implements ArrayInterface
{
    /**
     * Define constant variables
     */
    const GENERAL = 'INFO';
    const SPECIAL_DEALS = 'PROMO';
    const AVAILABLE_UPDATE = 'INSTALLED_UPDATE';
    const UNSUBSCRIBE_ALL = 'UNSUBSCRIBE_ALL';
    const TIPS_TRICKS = 'TIPS_TRICKS';

    /**
     * Converting option to array object
     *
     * @return array
     */
    public function toOptionArray()
    {
        $types = [
            [
                'value' => self::GENERAL,
                'label' => __('General Info')
            ],
            [
                'value' => self::SPECIAL_DEALS,
                'label' => __('Special Deals')
            ],
            [
                'value' => self::AVAILABLE_UPDATE,
                'label' => __('Available Updates')
            ],
            [
                'value' => self::TIPS_TRICKS,
                'label' => __('Magento Tips & Tricks')
            ],
            [
                'value' => self::UNSUBSCRIBE_ALL,
                'label' => __('Unsubscribe from all')
            ]
        ];

        return $types;
    }
}
