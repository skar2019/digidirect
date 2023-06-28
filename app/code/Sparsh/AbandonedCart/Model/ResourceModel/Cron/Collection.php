<?php
/**
 * Class Collection
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\AbandonedCart\Model\ResourceModel\Cron;

/**
 * Class Collection
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * IdFieldName
     *
     * @var string
     */
    protected $_idFieldName = 'schedule_id';

    /**
     * Initialize resource collection
     *
     * @return null
     */
    public function _construct()
    {
        $this->_init(\Magento\Cron\Model\Schedule::class, \Magento\Cron\Model\ResourceModel\Schedule::class);
    }
}
