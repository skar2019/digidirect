<?php

namespace Sparsh\CustomShippingMethod\Setup;

use Magento\Framework\Setup\InstallDataInterface;

/**
 * Class InstallData
 *
 * @package Sparsh\CustomShippingMethod\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $_eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Installs DB data for a module
     *
     * @param  \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param  \Magento\Framework\Setup\ModuleContextInterface   $context
     * @return void
     */
    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sparsh_handling_fee',
            [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Handling Fee',
                'required' => false,
                'default' => 0,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_for_price_rules' => true,
                'used_for_promo_rules' => true,
                'note' => 'Set product specific handling fee from here. It needs to have "Sparsh - Custom Shipping Method" method enabled from Configurations. Set "Type" as "Per Item" and "Calculate Handling Fee" as Percent/Fixed.',
                'frontend_class' => 'validate-number  validate-zero-or-greater'
            ]
        );
    }
}
