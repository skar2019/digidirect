<?php
namespace Ewave\AI\Model\ResourceModel\Queue\Queue;

use Ewave\AI\Api\Data\QueueInterface;

/**
 * Class Collection
 *
 * @package Ewave\AI\Model\ResourceModel\Queue\Queue
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ewave\AI\Model\Engine\Queue\Queue', 'Ewave\AI\Model\ResourceModel\Queue\Queue');
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Before Load
     *
     * @return void
     */
    protected function _beforeLoad()
    {
        $this->addFieldToFilter(QueueInterface::IS_RETIRED, ['eq' => 0]);
        parent::_beforeLoad();
    }
}
