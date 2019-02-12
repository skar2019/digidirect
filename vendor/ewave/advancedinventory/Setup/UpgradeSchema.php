<?php
namespace Ewave\AdvancedInventory\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var CatalogInventory\UpgradeSchema
     */
    protected $catalogInventoryUpgradeSchema;

    /**
     * UpgradeSchema constructor.
     * @param CatalogInventory\UpgradeSchema $catalogInventoryUpgradeSchema
     */
    public function __construct(
        \Ewave\AdvancedInventory\Setup\CatalogInventory\UpgradeSchema $catalogInventoryUpgradeSchema
    ) {
        $this->catalogInventoryUpgradeSchema = $catalogInventoryUpgradeSchema;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->catalogInventoryUpgradeSchema->upgrade($setup, $context);
        }

        $setup->endSetup();
    }
}
