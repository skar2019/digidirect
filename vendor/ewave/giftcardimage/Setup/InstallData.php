<?php
namespace Ewave\GiftCardImage\Setup;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\GiftCard\Model\Catalog\Product\Type\Giftcard;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * Constructor
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $categorySetup->addAttribute(
            Product::ENTITY,
            'giftcard_images',
            [
                'group' => 'Product Details',
                'type' => 'text',
                'label' => 'Gift Card Images',
                'input' => 'multiselect',
                'source' => 'Ewave\GiftCardImage\Model\Source\GiftCardImages',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'visible_on_front' => true,
                'apply_to' => Giftcard::TYPE_GIFTCARD,
                'used_in_product_listing' => true,
                'sort_order' => 10
            ]
        );
    }
}
