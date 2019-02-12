<?php

namespace Ewave\PreOrder\Setup;

use Ewave\PreOrder\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 *
 * @package Ewave\PreOrder\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $this->addPreorderNoteAttribute($eavSetup);

        $this->addPreorderCartLabelAttribute($eavSetup);
    }

    /**
     * Add ProductAttributeInterface::CODE_PREORDER_NOTE attribute to product attributes
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @return void
     */
    protected function addPreorderNoteAttribute($eavSetup)
    {
        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_PREORDER_NOTE,
            [
                'type'             => 'varchar',
                'backend'          => '',
                'frontend'         => '',
                'label'            => __('Pre-Order Note'),
                'input'            => 'hidden',
                'class'            => '',
                'source'           => '',
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible'          => false,
                'required'         => false,
                'user_defined'     => false,
                'default'          => '',
                'searchable'       => false,
                'filterable'       => false,
                'comparable'       => false,
                'visible_on_front' => false,
                'unique'           => false,
                'apply_to'         => '',
                'is_configurable'  => false
            ]
        );
    }

    /**
     * Add ProductAttributeInterface::CODE_PREORDER_CART_LABEL attribute to product attributes
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @return void
     */
    protected function addPreorderCartLabelAttribute($eavSetup)
    {
        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_PREORDER_CART_LABEL,
            [
                'type'             => 'varchar',
                'backend'          => '',
                'frontend'         => '',
                'label'            => __('Custom Pre-Order Cart Note'),
                'input'            => 'hidden',
                'class'            => '',
                'source'           => '',
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible'          => false,
                'required'         => false,
                'user_defined'     => false,
                'default'          => '',
                'searchable'       => false,
                'filterable'       => false,
                'comparable'       => false,
                'visible_on_front' => false,
                'unique'           => false,
                'apply_to'         => '',
                'is_configurable'  => false
            ]
        );
    }
}
