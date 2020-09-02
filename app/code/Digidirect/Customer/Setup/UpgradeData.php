<?php
namespace Digidirect\Customer\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    protected $customerSetupFactory;

    private $attributeSetFactory;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }


    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $customerSetup->addAttribute(
                    \Magento\Customer\Model\Customer::ENTITY,
                    'qff_first_initial',                    
                    [
                        'type' => 'varchar',
                        'label' => 'QFF First Initial',
                        'input' => 'text',
                        'required' => false,
                        'visible' => true,
                        'user_defined' => true,
                        'sort_order' => 210,
                        'position' => 210,
                        'system' => 0,
                    ]
                );
            $Attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'qff_first_initial')
            ->addData([
                'attribute_set_id' => 1,
                'attribute_group_id' => 1,
                'used_in_forms' => ['adminhtml_customer','checkout_register', 'customer_account_create', 'customer_account_edit','adminhtml_checkout'],
            ]);

            $Attribute->save();

            
        }

        $setup->endSetup();
    }
}