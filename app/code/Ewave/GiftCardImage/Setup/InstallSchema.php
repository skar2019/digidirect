<?php
namespace Ewave\GiftCardImage\Setup;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Create table 'ewave_giftcard_image'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable(GiftCardImage::MAIN_TABLE))
            ->addColumn(
                GiftCardImageInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Gift Card Image Id'
            )
            ->addColumn(
                GiftCardImageInterface::TITLE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Gift Card Title'
            )
            ->addColumn(
                GiftCardImageInterface::STATUS,
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'default' => '1'],
                'Status'
            )
            ->addColumn(
                GiftCardImageInterface::IMAGE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Image Path'
            )
            ->setComment('Ewave Gift Card Images');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'ewave_giftcard_quote_item'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('ewave_giftcard_quote_item'))
            ->addColumn(
                'quote_item_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Quote Item Id'
            )
            ->addColumn(
                'giftcard_image_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Gift Card Image Id'
            )
            ->addIndex(
                $setup->getIdxName(
                    'ewave_giftcard_quote_item',
                    ['quote_item_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['quote_item_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName('ewave_giftcard_quote_item', 'quote_item_id', 'quote_item', 'item_id'),
                'quote_item_id',
                $setup->getTable('quote_item'),
                'item_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Ewave Gift Card Image Quote Items');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'ewave_giftcard_order_item'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('ewave_giftcard_order_item'))
            ->addColumn(
                'order_item_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Order Item Id'
            )
            ->addColumn(
                'giftcard_image_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Gift Card Image Id'
            )
            ->addIndex(
                $setup->getIdxName(
                    'ewave_giftcard_order_item',
                    ['order_item_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['order_item_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName('ewave_giftcard_order_item', 'order_item_id', 'sales_order_item', 'item_id'),
                'order_item_id',
                $setup->getTable('sales_order_item'),
                'item_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Ewave Gift Card Image Order Items');
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
