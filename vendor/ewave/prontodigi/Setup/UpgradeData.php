<?php

namespace Ewave\ProntoDigi\Setup;

use Ewave\ProntoDigi\ProntoApi\Constants\CustomerAttributes;
use Ewave\ProntoDigi\ProntoApi\Constants\Products as ProductConstants;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\Customer as CustomerEntity;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var ModuleContextInterface
     */
    protected $context;

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory, CustomerSetupFactory $customerSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->context = $context;
        $setup->startSetup();

        if ($this->compareVersions('1.0.1')) {
            $this->upgradeTo101($setup);
        }

        if ($this->compareVersions('1.0.2')) {
            $this->upgradeTo102($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    protected function upgradeTo102(ModuleDataSetupInterface $setup)
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup'=> $setup]);
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            CustomerEntity::ENTITY,
            CustomerAttributes::PRONTO_ACCOUNT_ID,
            [
                'type'         => 'varchar',
                'label'        => 'Pronto Account ID',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => false,
                'position'     => 100,
                'system'       => 0,
                'used_in_forms', ['adminhtml_customer', 'customer_form_attribute']
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(
            CustomerEntity::ENTITY,
            CustomerAttributes::PRONTO_ACCOUNT_ID
        );
        $attribute->setData('used_in_forms',['adminhtml_customer', 'customer_form_attribute']);
        $attribute->save();
        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            CustomerAttributes::PRONTO_ACCOUNT_ID
        );

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            CustomerAttributes::PRONTO_ACCOUNT_NAME,
            [
                'type'         => 'varchar',
                'label'        => 'Pronto Account Name',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => false,
                'position'     => 110,
                'system'       => 0,
                'used_in_forms', ['adminhtml_customer', 'customer_form_attribute']
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(
            CustomerEntity::ENTITY,
            CustomerAttributes::PRONTO_ACCOUNT_NAME
        );
        $attribute->setData('used_in_forms',['adminhtml_customer', 'customer_form_attribute']);
        $attribute->save();
        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            CustomerAttributes::PRONTO_ACCOUNT_NAME
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    protected function upgradeTo101(ModuleDataSetupInterface $setup)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductConstants::PRODUCT_APN,
            [
                'group'          => 'Product Details',
                'type'           => 'varchar',
                'label'          => 'APN',
                'input'          => 'text',
                'required'       => false,
                'sort_order'     => 100,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => true,
            ]
        );
    }

    /**
     * Compare versions
     *
     * @param string $new
     * @return bool
     */
    protected function compareVersions($new)
    {
        return version_compare($this->context->getVersion(), $new, '<');
    }
}