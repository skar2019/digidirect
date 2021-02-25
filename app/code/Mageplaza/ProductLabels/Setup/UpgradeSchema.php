<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Zend_Db_Exception;

/**
 * Class UpgradeSchema
 * @package Mageplaza\ProductLabels\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        if ($installer->tableExists('mageplaza_productlabels_rule_meta')) {
            $installer->getConnection()->dropTable($installer->getTable('mageplaza_productlabels_rule_meta'));
        }

        $table = $installer->getConnection()
            ->newTable($installer->getTable('mageplaza_productlabels_rule_meta'))
            ->addColumn('meta_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'primary'  => true,
                'unsigned' => true
            ], 'Meta Data ID')
            ->addColumn('rule_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Rule ID')
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )
            ->addColumn('template_url', Table::TYPE_TEXT, 255, ['nullable => false'], 'Category Template Path')
            ->addColumn('img_url', Table::TYPE_TEXT, 255, ['nullable => false'], 'Category Image Label Path')
            ->addColumn('label', Table::TYPE_TEXT, 255, ['nullable => false'], 'Category Label')
            ->addColumn('label_style', Table::TYPE_TEXT, 255, ['nullable => false'], 'Category Label Style')
            ->addColumn('label_fontsize', Table::TYPE_TEXT, 255, ['nullable => false'], 'Category Label Font Size')
            ->addColumn('label_position', Table::TYPE_TEXT, 255, ['nullable => false'], 'Category Label Position')
            ->addColumn('custom_css', Table::TYPE_TEXT, 255, ['nullable => false'], 'Category Custom Css')
            ->addColumn('from_date', Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null], 'From')
            ->addColumn('to_date', Table::TYPE_INTEGER, null, ['nullable' => true, 'default' => null], 'To')
            ->addColumn('customer_group_id', Table::TYPE_TEXT, null, [], 'Customer Group Ids')
            ->addColumn('store_id', Table::TYPE_TEXT, null, [], 'Store Id')
            ->addColumn('stop_process', Table::TYPE_TEXT, null, [], 'Stop Process')
            ->addColumn('priority', Table::TYPE_TEXT, 255, ['nullable => false'], 'Priority')
            ->addIndex($installer->getIdxName('mageplaza_productlabels_rule_meta', ['rule_id']), ['rule_id'])
            ->addForeignKey(
                $installer->getFkName(
                    'mageplaza_productlabels_rule_meta',
                    'rule_id',
                    'mageplaza_productlabels_rule',
                    'rule_id'
                ),
                'rule_id',
                $installer->getTable('mageplaza_productlabels_rule'),
                'rule_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Meta Data Table');

        $installer->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
