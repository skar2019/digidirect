<?php

namespace Ewave\ProntoDigi\Setup;

use Ewave\ProntoDigi\ProntoApi\Constants\Products as ProductConstants;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    const OPTIONS_STOCK_STATUS = [
        'S' => 'S',
        'I' => 'I',
        'K' => 'K',
        'Z' => 'Z'
    ];

    const OPTIONS_STOCK_CONDITION = [
        'O' => 'O',
        'T' => 'T',
        'other' => 'Other',
    ];

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_UOM,
            [
                'group'          => 'Product Details',
                'type'           => 'varchar',
                'label'          => 'Unit of Measure',
                'input'          => 'text',
                'required'       => false,
                'sort_order'     => 100,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_CONVERSION,
            [
                'group'          => 'Product Details',
                'type'           => 'int',
                'label'          => 'Conversion',
                'input'          => 'text',
                'frontend_class' => 'validate-digits',
                'required'       => false,
                'sort_order'     => 110,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_BARCODE1,
            [
                'group'          => 'Product Details',
                'type'           => 'text',
                'label'          => 'Barcode1',
                'input'          => 'text',
                'required'       => false,
                'sort_order'     => 120,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_BARCODE2,
            [
                'group'          => 'Product Details',
                'type'           => 'text',
                'label'          => 'Barcode2',
                'input'          => 'text',
                'required'       => false,
                'sort_order'     => 130,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_BARCODE3,
            [
                'group'          => 'Product Details',
                'type'           => 'text',
                'label'          => 'Barcode3',
                'input'          => 'text',
                'required'       => false,
                'sort_order'     => 140,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_BARCODE4,
            [
                'group'          => 'Product Details',
                'type'           => 'text',
                'label'          => 'Barcode4',
                'input'          => 'text',
                'required'       => false,
                'sort_order'     => 150,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_STOCK_STATUS,
            [
                'group' => 'Product Details',
                'type' => 'int',
                'label' => 'Pronto Stock Status',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'required' => false,
            ]
        );

        $this->addAttributeOptions(
            $eavSetup,
            self::OPTIONS_STOCK_STATUS,
            ProductConstants::PRODUCT_ATTRIBUTE_STOCK_STATUS
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION,
            [
                'group' => 'Product Details',
                'type' => 'int',
                'label' => 'Stock Condition',
                'input' => 'select',
                'source' => Table::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'required' => false,
            ]
        );

        $this->addAttributeOptions(
            $eavSetup,
            self::OPTIONS_STOCK_CONDITION,
            ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_STOCK_GROUP,
            [
                'group' => 'Product Details',
                'type' => 'varchar',
                'label' => 'Stock Group',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'required' => false,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_STOCK_BRAND,
            [
                'group' => 'Product Details',
                'type' => 'int',
                'label' => 'Brand',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'required' => false,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_ATTRIBUTE_EXCLUDE_FROM_INTEGRATION,
            [
                'group' => 'Product Details',
                'type' => 'int',
                'label' => 'Exclude From Integration',
                'input' => 'boolean',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'required' => false,
            ]
        );
    }

    /**
     * @param EavSetup $eavSetup
     * @param array    $options
     * @param          $code
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addAttributeOptions(EavSetup $eavSetup, array $options, $attributeCode)
    {
        if (!$options) {
            return $this;
        }
        $values = [];
        foreach ($options as $code => $label) {
            $values[$code] = __($label);
        }

        $attributeId = $eavSetup->getAttributeId(
            Product::ENTITY,
            $attributeCode
        );

        $eavSetup->addAttributeOption(['values' => $values, 'attribute_id' => $attributeId]);

        return $this;
    }
}
