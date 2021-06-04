<?php
namespace Ewave\ProductAttachment\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @package Ewave\Faq\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->updateRelation($setup);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    protected function updateRelation(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('ewave_product_attachment_relation');
        $setup->getConnection()->addColumn(
            $tableName,
            'attached',
            [
                'type' => Table::TYPE_BOOLEAN,
                'unsigned' => false,
                'nullable' => false,
                'default'  => 0,
                'comment' => 'Attached',
            ]
        );
        return $this;
    }
}
