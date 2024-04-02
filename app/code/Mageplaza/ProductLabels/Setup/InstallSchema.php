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
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\ProductLabels\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists('mageplaza_productlabels_rule')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_productlabels_rule'))
                ->addColumn('rule_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ], 'Rule Id')
                ->addColumn('name', Table::TYPE_TEXT, 255, [], 'Name')
                ->addColumn('enabled', Table::TYPE_INTEGER, 1, [], 'Status')
                ->addColumn('store_ids', Table::TYPE_TEXT, null, ['nullable' => false, 'unsigned' => true,], 'Store Id')
                ->addColumn('customer_group_ids', Table::TYPE_TEXT, null, [], 'Customer Group')
                ->addColumn('priority', Table::TYPE_SMALLINT, null, [
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => '0'
                ], 'Priority')
                ->addColumn('label_template', Table::TYPE_TEXT, 255, [
                    'unsigned' => false,
                    'nullable' => true
                ], 'Label Template')
                ->addColumn('label_image', Table::TYPE_TEXT, 255, [], 'Product Label Image')
                ->addColumn('label', Table::TYPE_TEXT, 255, [
                    'unsigned' => false,
                    'nullable' => true
                ], 'Product Text Label')
                ->addColumn('label_font', Table::TYPE_TEXT, 255, [], 'Product Label Font Family')
                ->addColumn('label_font_size', Table::TYPE_TEXT, 255, [], 'Product Label Font Size')
                ->addColumn('label_color', Table::TYPE_TEXT, 255, [], 'Product Label Color')
                ->addColumn('label_css', Table::TYPE_TEXT, '2M', [], 'Product Label Css')
                ->addColumn('label_position', Table::TYPE_TEXT, '2M', [], 'Product Label Position')
                ->addColumn('label_position_grid', Table::TYPE_TEXT, '2M', [], 'Position Grid Position Product Page')
                ->addColumn('same', Table::TYPE_INTEGER, 1, [], 'Same Design Product Page')
                ->addColumn('list_template', Table::TYPE_TEXT, 255, [
                    'unsigned' => false,
                    'nullable' => true
                ], 'Label Template Listing Page')
                ->addColumn('list_image', Table::TYPE_TEXT, 255, [], 'Label Image Listing Page ')
                ->addColumn('list_label', Table::TYPE_TEXT, 255, [
                    'unsigned' => false,
                    'nullable' => true
                ], 'Text Label Listing Page ')
                ->addColumn('list_font', Table::TYPE_TEXT, 255, [], 'Listing Label Font Family')
                ->addColumn('list_font_size', Table::TYPE_TEXT, 255, [], 'Listing Label Font Size')
                ->addColumn('list_color', Table::TYPE_TEXT, 255, [], 'Listing Label Color')
                ->addColumn('list_css', Table::TYPE_TEXT, '2M', [], 'Listing Label Css')
                ->addColumn('list_position', Table::TYPE_TEXT, '2M', [], 'Label Position Listing Page ')
                ->addColumn('list_position_grid', Table::TYPE_TEXT, '2M', [], 'Position Grid Position Listing Page')
                ->addColumn('conditions_serialized', Table::TYPE_TEXT, '2M', [], 'Conditions Serialized')
                ->addColumn('bestseller', Table::TYPE_INTEGER, 1, [], 'Best Seller')
                ->addColumn('new', Table::TYPE_SMALLINT, null, [], 'New')
                ->addColumn('on_sale', Table::TYPE_SMALLINT, null, [], 'On Sale')
                ->addColumn('limit', Table::TYPE_TEXT, 255, [], 'Limit')
                ->addColumn('from_date', Table::TYPE_DATE, null, ['nullable' => true, 'default' => null], 'From')
                ->addColumn('to_date', Table::TYPE_DATE, null, ['nullable' => true, 'default' => null], 'To')
                ->addColumn('stop_process', Table::TYPE_INTEGER, 1, [], 'Stop further processing')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Rule Updated At')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Rule Created At')
                ->setComment('ProductLabels Rule');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
