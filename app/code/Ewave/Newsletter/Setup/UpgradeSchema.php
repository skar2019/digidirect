<?php

namespace Ewave\Newsletter\Setup;

use Ewave\Newsletter\Api\Data\SubscriberInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Ewave\Newsletter\Model\ResourceModel\Subscriber as SubscriberResource;

/**
 * Class UpgradeSchema
 * @package Ewave\Newsletter\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->createSubscriber($setup);
        }
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function createSubscriber(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable(SubscriberResource::TABLE)
        )->addColumn(
            SubscriberInterface::SUBSCRIBER_ID,
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Subscriber ID'
        )->addColumn(
            SubscriberInterface::FIRSTNAME,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'First Name'
        )->addColumn(
            SubscriberInterface::LASTNAME,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Last Name'
        )->addIndex(
            $installer->getIdxName(SubscriberResource::TABLE, [SubscriberInterface::SUBSCRIBER_ID]),
            [SubscriberInterface::SUBSCRIBER_ID]
        )->addForeignKey(
            $installer->getFkName(
                SubscriberResource::TABLE,
                SubscriberInterface::SUBSCRIBER_ID,
                'newsletter_subscriber',
                'subscriber_id'
            ),
            SubscriberInterface::SUBSCRIBER_ID,
            $installer->getTable('newsletter_subscriber'),
            'subscriber_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
        return $this;
    }
}
