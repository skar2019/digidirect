<?php

namespace Sparsh\CustomShippingMethod\Setup;

use Magento\Catalog\Model\Product;

/**
 * Class Uninstall
 *
 * @package Sparsh\CustomShippingMethod\Setup
 */
class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $_eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory              $eavSetupFactory
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Uninstall DB schema for a module
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @return void
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $eavSetup = $this->_eavSetupFactory->create();
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $eavSetup->removeAttribute($entityTypeId, 'sparsh_handling_fee');
        $setup->endSetup();
    }
}
