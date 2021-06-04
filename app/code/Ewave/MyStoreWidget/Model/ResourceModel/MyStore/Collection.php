<?php
namespace Ewave\MyStoreWidget\Model\ResourceModel\MyStore;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Ewave\MyStoreWidget\Model\ResourceModel\MyStore
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ewave\MyStoreWidget\Model\MyStore', 'Ewave\MyStoreWidget\Model\ResourceModel\MyStore');
    }
}
