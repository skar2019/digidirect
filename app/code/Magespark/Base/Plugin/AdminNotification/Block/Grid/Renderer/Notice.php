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


namespace MageSpark\Base\Plugin\AdminNotification\Block\Grid\Renderer;

use Magento\AdminNotification\Block\Grid\Renderer\Notice as NativeNotice;
use Magento\Framework\DataObject;

/**
 * Class Notice
 *
 * @package MageSpark\Base\Plugin\AdminNotification\Block\Grid\Renderer
 */
class Notice
{
    /**
     * Get the grid data with logo
     *
     * @param NativeNotice $subject
     * @param \Closure $proceed
     * @param DataObject $row
     * @return mixed|string
     */
    public function aroundRender(
        NativeNotice $subject,
        \Closure $proceed,
        DataObject $row
    ) {
        $result = $proceed($row);

        $magesparkLogo = $row->getData('is_magespark') ? ' magespark-grid-logo' : '';
        $result = '<div class="msbase-grid-message' . $magesparkLogo .'">' . $result . '</div>';

        return  $result;
    }
}
