<?php

namespace Ewave\OutOfStockNotif\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 */
class UpgradeData implements UpgradeDataInterface
{
    const ATTRIBUTE_NAME_DISPLAY_AVAILABEL_DATE = 'display_availability_date';
    const ATTRIBUTE_NAME_AVAILABEL_DATE = 'availability_date';
    const ATTRIBUTE_NAME_SOLD_OUT_MESSAGE = 'ewave_sold_out';

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ModuleContextInterface
     */
    protected $context;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->context = $context;
        if ($this->compareVersions('1.0.1')) {
            $this->addProductAvailabilityDateAttributes($setup);
        }

        if ($this->compareVersions('1.0.2')) {
            $this->changeScopeAvailabilityDateAttributes($setup);
        }

        if ($this->compareVersions('1.0.3')) {
            $this->addSoldOutAttribute($setup);
        }
    }

    /**
     * @param string $version
     * @return bool
     */
    protected function compareVersions($version)
    {
        return version_compare($this->context->getVersion(), $version, '<');
    }

    /**
     * @param $setup
     * @return mixed
     */
    public function addProductAvailabilityDateAttributes($setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_NAME_DISPLAY_AVAILABEL_DATE,
            [
                'type' => 'int',
                'label' => 'Display The Availability Date',
                'input' => 'boolean',
                'source' => Boolean::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'backend' => '',
                'visible' => true,
                'required' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'unique' => false,
                'default' => 0
            ]
        )->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_NAME_AVAILABEL_DATE,
            [
                'type' => 'datetime',
                'label' => ' Availability Date',
                'input' => 'date',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'backend' => Datetime::class,
                'visible' => true,
                'required' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'unique' => false,
            ]
        );
        return $setup;
    }

    public function changeScopeAvailabilityDateAttributes($setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $attributesCodes = [
            self::ATTRIBUTE_NAME_DISPLAY_AVAILABEL_DATE,
            self::ATTRIBUTE_NAME_AVAILABEL_DATE
        ];
        foreach ($attributesCodes as $attribute) {
            $eavSetup->updateAttribute(Product::ENTITY, $attribute, 'is_global', ScopedAttributeInterface::SCOPE_STORE);
            $eavSetup->updateAttribute(Product::ENTITY, $attribute, 'is_required', false);
        }
        return $setup;
    }

    public function addSoldOutAttribute($setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_NAME_SOLD_OUT_MESSAGE,
            [
                'type' => 'int',
                'label' => 'Display Sold Out Message',
                'input' => 'boolean',
                'source' => Boolean::class,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'backend' => '',
                'visible' => true,
                'required' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'unique' => false,
                'default' => 0
            ]
        );
        return $setup;
    }
}
