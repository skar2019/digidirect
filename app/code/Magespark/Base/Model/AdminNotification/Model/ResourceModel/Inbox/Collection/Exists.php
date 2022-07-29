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

namespace MageSpark\Base\Model\AdminNotification\Model\ResourceModel\Inbox\Collection;

use Magento\AdminNotification\Model\ResourceModel\Inbox\Collection;

/**
 * Class Exists
 *
 * @package MageSpark\Base\Model\AdminNotification\Model\ResourceModel\Inbox\Collection
 */
class Exists extends Collection
{
    /**
     * Filter the url and get size
     *
     * @param \SimpleXMLElement $item
     * @return bool
     */
    public function execute(\SimpleXMLElement $item)
    {
        $this->addFieldToFilter('url', (string)$item->link);

        return $this->getSize() > 0;
    }

    /**
     * Converting xml data to string
     *
     * @param \SimpleXMLElement $data
     * @return string
     */
    private function convertString(\SimpleXMLElement $data)
    {
        $data = htmlspecialchars((string)$data);
        return $data;
    }
}
