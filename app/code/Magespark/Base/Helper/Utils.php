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

namespace MageSpark\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Utils
 *
 * @package MageSpark\Base\Helper
 */
class Utils extends AbstractHelper
{
    /**
     * Exit from function
     * @param int $code
     */
    public function _exit($code = 0)
    {
        /** @codingStandardsIgnoreStart */
        exit($code);
        /** @codingStandardsIgnoreEnd */
    }
    /**
     * This function is used for printing the variable
     * @param $a
     */
    public function _echo($a)
    {
        /** @codingStandardsIgnoreStart */
        echo $a;
        /** @codingStandardsIgnoreEnd */
    }
}
