<?php

namespace Ewave\Newsletter\Model\ResourceModel;

use Ewave\Newsletter\Api\Data\SubscriberInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Subscriber extends AbstractDb
{
    const TABLE = 'ewave_newsletter_subscriber';

    /**
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, SubscriberInterface::SUBSCRIBER_ID);
    }
}
