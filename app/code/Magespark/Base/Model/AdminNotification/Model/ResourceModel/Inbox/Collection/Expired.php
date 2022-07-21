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
 * Class Expired
 * @package MageSpark\Base\Model\AdminNotification\Model\ResourceModel\Inbox\Collection
 */
class Expired extends Collection
{
    /**
     * Initilization query with expiration date
     *
     * @return $this|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('is_remove', 0)
             ->addFieldToFilter('is_magespark', 1)
             ->addFieldToFilter('expiration_date', ['neq' => 'NULL'])
             ->addFieldToFilter('expiration_date', ['lt' => 'NOW()']);

        return $this;
    }
}
