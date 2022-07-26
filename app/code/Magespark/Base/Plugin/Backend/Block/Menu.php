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

namespace MageSpark\Base\Plugin\Backend\Block;

use Magento\Backend\Block\Menu as NativeMenu;
use Magento\Backend\Block\Template;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Menu
 *
 * @package MageSpark\Base\Plugin\Backend\Block
 */
class Menu
{
    const MAX_ITEMS = 10;

    /**
     * @param NativeMenu $subject
     * @param $html
     * @return string
     * @throws LocalizedException
     */
    public function afterToHtml(NativeMenu $subject, $html)
    {
        $js = $subject->getLayout()->createBlock(Template::class)
            ->setTemplate('MageSpark_Base::js.phtml')
            ->toHtml();

        return $html . $js;
    }
}
