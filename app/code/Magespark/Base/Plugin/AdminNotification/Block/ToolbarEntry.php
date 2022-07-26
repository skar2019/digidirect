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

namespace MageSpark\Base\Plugin\AdminNotification\Block;

use Magento\AdminNotification\Block\ToolbarEntry as NativeToolbarEntry;

/**
 * Class ToolbarEntry
 *
 * @package MageSpark\Base\Plugin\AdminNotification\Block
 */
class ToolbarEntry
{
    const MAGESPARK_ATTRIBUTE = ' data-msbase-logo="1"';

    /**
     * Return the collection of unread notification
     *
     * @param NativeToolbarEntry $subject
     * @param $html
     * @return mixed
     */
    public function afterToHtml(
        NativeToolbarEntry $subject,
        $html
    ) {
        $collection = $subject->getLatestUnreadNotifications()
            ->clear()
            ->addFieldToFilter('is_magespark', 1);

        foreach ($collection as $item) {
            $search = 'data-notification-id="' . $item->getId() . '"';
            $html = str_replace($search, $search . self::MAGESPARK_ATTRIBUTE, $html);
        }


        return $html;
    }
}
