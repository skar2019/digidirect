<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_PreOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\PreOrder\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    const BSS_PRE_ORDER_TAB = 'Pre Order';
    const BSS_PRE_ORDER_ATTRIBUTES = [
        'preorder' => 10,
        'message' => 20,
        'availability_message' => 40,
        'pre_oder_to_date' => 50,
        'pre_oder_from_date' => 60
    ];

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * UpgradeData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Upgrade Eav Attribute
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $groupCollection = $this->groupCollectionFactory->create();
        $arrGroups = [];
        foreach ($groupCollection as $group) {
            if (in_array($group->getAttributeGroupName(), $arrGroups)) {
                continue;
            }
            array_push($arrGroups, $group->getAttributeGroupName());
        }

        $eavSetup = $this->eavSetupFactory->create();
        $this->updateAttribute($eavSetup);

        if (version_compare($context->getVersion(), '1.1.6', '<')) {
            $this->upgradeVersion116($eavSetup);
        }
        if (version_compare($context->getVersion(), '1.1.8', '<')) {
            $this->upgradeVersion118($setup, $eavSetup);
        }

        $this->updateNotePreOrder($eavSetup);
    }

    /**
     * Upgrade Eav Attribute
     *
     * @param ModuleDataSetupInterface $setup
     */
    protected function updateAttribute($eavSetup)
    {
        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'preorder',
            'source',
            \Bss\PreOrder\Model\Attribute\Source\Order::class
        );

        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\product::ENTITY,
            'preorder',
            'used_in_product_listing',
            true
        );

        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\product::ENTITY,
            'message',
            'used_in_product_listing',
            true
        );

        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\product::ENTITY,
            'preorder',
            'apply_to',
            'simple'
        );

        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\product::ENTITY,
            'message',
            'apply_to',
            'simple'
        );
    }

    /**
     * Upgrade version 1.1.6
     *
     * @param EavSetup $eavSetup
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    protected function upgradeVersion116($eavSetup)
    {
        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'availability_message'
        );
        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'pre_order_date'
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'availability_message',
            [
                'type' => 'text',
                'label' => 'Availability Message',
                'input' => 'text',
                'required' => false,
                'sort_order' => 58,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'apply_to' => 'simple',
                'note' => __("{date} can be used as Availability Date, {preorder_date}
                can be used as Availability Pre-order Date")
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'pre_oder_from_date',
            [
                'type' => 'datetime',
                'label' => 'Availability Pre-order From Date',
                'input' => 'date',
                'required' => false,
                'sort_order' => 58,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'apply_to' => 'simple'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'pre_oder_to_date',
            [
                'type' => 'datetime',
                'label' => 'Availability Pre-order To Date',
                'input' => 'date',
                'required' => false,
                'sort_order' => 58,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'apply_to' => 'simple'
            ]
        );
    }

    /**
     * @param EavSetup $eavSetup
     */
    protected function updateNotePreOrder($eavSetup)
    {
        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'message',
            [
                'note' => __("{preorder_date} can be used as the Pre-order Button Availability period.
                If this field is blank, the default message edited in the configuration will be displayed.")
            ]
        );
        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'availability_message',
            [
                'note' => __("{preorder_date} can be used as the Pre-order Button Availability period.")
            ]
        );
    }

    /**
     * Upgrade version to 1.1.8
     * Set attribute group and set use listing for attribute
     * @param ModuleDataSetupInterface $setup
     * @param \Magento\EAV\Setup\EavSetup $eavSetup
     */
    protected function upgradeVersion118($setup, $eavSetup)
    {
        $arrSetUseListingAttribute = [
            'availability_message',
            'pre_oder_from_date',
            'pre_oder_to_date'

        ];
        // set use listing for attribute
        foreach ($arrSetUseListingAttribute as $attribute) {
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\product::ENTITY,
                $attribute,
                'used_in_product_listing',
                true
            );
        }
        //Assign attribute for Tab Pre Order
        $this->assignAttributeToTab($setup, $eavSetup);
        // Remove attribute Restock
        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'restock'
        );
        //add column to sales order to get list product preorder in order
        $table = $setup->getTable('sales_order');
        $connection = $setup->getConnection();
        $connection->addColumn(
            $table,
            'product_pre_order',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'comment' => 'Product PreOrder'
            ]
        );
        $connection->modifyColumn(
            $table,
            'product_pre_order',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '4G'
            ]
        );
        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\product::ENTITY,
            'pre_oder_from_date',
            [
                'note' => __("This config only works when the product is set Pre-order: Yes")
            ]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param \Magento\EAV\Setup\EavSetup $eavSetup
     * @throws LocalizedException
     */
    protected function assignAttributeToTab($setup, $eavSetup)
    {
        $select = $setup->getConnection()->select()->from(
            $setup->getTable('eav_attribute_set')
        )->where(
            'entity_type_id = :entity_type_id'
        );
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $sets = $setup->getConnection()->fetchAll($select, ['entity_type_id' => $entityTypeId]);
        foreach ($sets as $set) {
            if (!$eavSetup->getAttributeGroup(Product::ENTITY, $set['attribute_set_id'], self::BSS_PRE_ORDER_TAB)) {
                $eavSetup->addAttributeGroup($entityTypeId, $set['attribute_set_id'], self::BSS_PRE_ORDER_TAB);
            }
            foreach (self::BSS_PRE_ORDER_ATTRIBUTES as $code => $sortOrder) {
                $eavSetup->addAttributeToSet(
                    $entityTypeId,
                    $set['attribute_set_id'],
                    self::BSS_PRE_ORDER_TAB,
                    $code,
                    $sortOrder
                );
            }
        }
    }
}
