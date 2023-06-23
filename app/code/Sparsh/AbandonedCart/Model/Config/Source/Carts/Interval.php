<?php
/**
 * Class Interval
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\AbandonedCart\Model\Config\Source\Carts;

/**
 * Class Interval
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Interval implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Available times.
     *
     * @var array
     */
    public $times
        = [
            1,
            2,
            3,
            4,
            5,
            6,
            12,
            24,
            36,
            48,
            60,
            72,
            84,
            96,
            108,
            120,
            240,
        ];

    /**
     * Send to campain options hours.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = $row = [];
        $i = 0;
        foreach ($this->times as $one) {
            if ($i == 0) {
                $row = [
                    'value' => $one,
                    'label' => $one . __(' Hour'),
                ];
            } else {
                $row = [
                    'value' => $one,
                    'label' => $one . __(' Hours'),
                ];
            }
            $result[] = $row;
            ++$i;
        }

        return $result;
    }
}
