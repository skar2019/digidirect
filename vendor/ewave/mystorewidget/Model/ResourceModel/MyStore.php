<?php
namespace Ewave\MyStoreWidget\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class MyStore
 * @package Ewave\MyStoreWidget\Model\ResourceModel
 */
class MyStore extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ewave_mystorewidget_customer_store', 'id');
    }
}
