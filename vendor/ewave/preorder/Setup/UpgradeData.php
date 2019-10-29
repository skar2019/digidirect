<?php

namespace Ewave\PreOrder\Setup;

use Ewave\PreOrder\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), "1.0.1", "<")) {
            $this->addProductAvailabilityDateAttribute($eavSetup);
        }
    }

    /**
     * @param EavSetupFactory $eavSetup
     * @return $this
     */
    protected function addProductAvailabilityDateAttribute($eavSetup)
    {
        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::CODE_PRODUCT_AVAILABILITY_DATE,
            [
                'type' => 'datetime',
                'backend' => '',
                'frontend' => '',
                'label' => __('Product Availability Date'),
                'input' => 'hidden',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => '',
                'is_configurable' => false,
            ]
        );
        return $this;
    }
}
