<?php

namespace Ewave\Digi\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product\Attribute\Repository as AttributeRepository;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Setup\Exception;
use Magento\Swatches\Helper\Media;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class UpgradeSchema
 * @package Ewave\Digi\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var AttributeSetFactory
     */
    protected $attributeSetFactory;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var Config
     */
    protected $productMediaConfig;

    /**
     * @var File
     */
    protected $driverFile;

    /**
     * @var Media
     */
    protected $swatchHelper;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var KeyFeatureEntitySetup
     */
    protected $keyFeatureEntitySetup;

    /**
     * UpgradeData constructor.
     * @param AttributeSetFactory $attributeSetFactory
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeRepository $attributeRepository
     * @param State $state
     * @param Filesystem $filesystem
     * @param Config $productMediaConfig
     * @param File $driverFile
     * @param Media $swatchHelper
     * @param Json $serializer
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param KeyFeatureEntitySetupFactory $keyFeatureEntitySetup
     */
    public function __construct(
        AttributeSetFactory $attributeSetFactory,
        EavSetupFactory $eavSetupFactory,
        AttributeRepository $attributeRepository,
        State $state,
        Filesystem $filesystem,
        Config $productMediaConfig,
        File $driverFile,
        Media $swatchHelper,
        Json $serializer,
        CategoryFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        KeyFeatureEntitySetupFactory $keyFeatureEntitySetup
    ) {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeRepository = $attributeRepository;
        $this->state = $state;
        $this->filesystem = $filesystem;
        $this->productMediaConfig = $productMediaConfig;
        $this->driverFile = $driverFile;
        $this->swatchHelper = $swatchHelper;
        $this->serializer = $serializer;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->keyFeatureEntitySetup = $keyFeatureEntitySetup;

        try {
            $state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        } catch (LocalizedException $e) {

        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->upgradeTo101($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->upgradeTo102($setup);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->upgradeTo103($setup);
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->upgradeTo104($setup);
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->upgradeTo105($setup);
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $this->upgradeTo106($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    public function upgradeTo101(ModuleDataSetupInterface $setup)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $idGroup = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'General Information');

        $attributesData = [
            'category_image_background_desktop' => [
                'type' => 'varchar',
                'label' => 'Category Background Image Desktop',
                'input' => 'image',
                'backend' => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class,
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'sort_order' => 10,
            ],
            'category_image_background_mobile' => [
                'type' => 'varchar',
                'label' => 'Category Background Image Mobile',
                'input' => 'image',
                'backend' => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class,
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'sort_order' => 20,
            ],
        ];

        foreach ($attributesData as $code => $attrData) {
            $eavSetup->addAttribute($entityTypeId, $code, $attrData);
            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $idGroup,
                $code,
                $attrData['sort_order']
            );
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    public function upgradeTo102(ModuleDataSetupInterface $setup)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $idGroup = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Images');

        $attributesData = [
            'product_image_background_desktop' => [
                'type' => 'varchar',
                'label' => 'Product Background Image Desktop',
                'input' => 'media_image',
                'backend' => '',
                'frontend' => \Magento\Catalog\Model\Product\Attribute\Frontend\Image::class,
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'sort_order' => 10,
            ],
            'product_image_background_mobile' => [
                'type' => 'varchar',
                'label' => 'Product Background Image Mobile',
                'input' => 'media_image',
                'backend' => '',
                'frontend' => \Magento\Catalog\Model\Product\Attribute\Frontend\Image::class,
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'sort_order' => 20,
            ],
        ];

        foreach ($attributesData as $code => $attrData) {
            $eavSetup->addAttribute($entityTypeId, $code, $attrData);
            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $idGroup,
                $code,
                $attrData['sort_order']
            );
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     */
    public function upgradeTo103(ModuleDataSetupInterface $setup)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $idGroup = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'General Information');

        $attributesData = [
            'pronto_stock_status' => [
                'type' => 'int',
                'label' => 'Pronto Stock Status',
                'input' => 'boolean',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'default' => 0,
                'required' => false,
                'user_defined' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'sort_order' => 10,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
            ],
            'whats_in_the_box' => [
                'type' => 'varchar',
                'label' => 'What\'s in the box',
                'input' => 'textarea',
                'required' => false,
                'user_defined' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'sort_order' => 20,
            ],
            'specification' => [
                'type' => 'text',
                'label' => 'Specification',
                'input' => 'textarea',
                'required' => false,
                'user_defined' => true,
                'wysiwyg_enabled' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'sort_order' => 30,
            ],
            'mount' => [
                'type' => 'int',
                'label' => 'Mount type',
                'input' => 'select',
                'required' => false,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'user_defined' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'sort_order' => 40,
                'visible' => true,
                'searchable' => false,
                'comparable' => true,
                'filterable' => true,
                'filterable_in_search' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'option' => [
                    'values' => [
                        'AF Mount',
                        'F Mount',
                        'Z Mount'
                    ]
                ]
            ]
        ];

        foreach ($attributesData as $code => $attrData) {
            $eavSetup->addAttribute($entityTypeId, $code, $attrData);
            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $idGroup,
                $code,
                $attrData['sort_order']
            );
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws \Exception
     */
    public function upgradeTo104(ModuleDataSetupInterface $setup)
    {
        try {
            $entitySetup = $this->keyFeatureEntitySetup->create(['setup' => $setup]);
            $entitySetup->installEntities();
        } catch (\Exception $e) {

        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws \Exception
     */
    public function upgradeTo105(ModuleDataSetupInterface $setup)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $idGroup = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'General Information');

        $attrData = [
            'type' => 'varchar',
            'label' => 'Key Features',
            'input' => 'multiselect',
            'required' => false,
            'source' => \Ewave\Digi\Model\Source\KeyFeatures::class,
            'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
            'user_defined' => true,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'sort_order' => 50,
            'visible' => true,
            'searchable' => false,
            'comparable' => false,
            'filterable' => false,
            'filterable_in_search' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
        ];

        $eavSetup->addAttribute($entityTypeId, 'key_features', $attrData);
        $eavSetup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $idGroup,
            'key_features',
            $attrData['sort_order']
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws \Exception
     */
    public function upgradeTo106(ModuleDataSetupInterface $setup)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $idGroup = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'General Information');

        $attributesData = [
            'subject' => [
                'type' => 'int',
                'label' => 'Subject',
                'input' => 'select',
                'required' => false,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'user_defined' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'sort_order' => 100,
                'visible' => true,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'option' => [
                    'values' => [
                        'Travel',
                        'Landscape',
                        'Wedding & Events',
                        'Portrait',
                        'Family',
                        'Nature & Wildlife',
                        'Sports & Action',
                        'Video Architecture Interior',
                    ]
                ]
            ],
            'experience' => [
                'type' => 'int',
                'label' => 'Experience',
                'input' => 'select',
                'required' => false,
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'user_defined' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'sort_order' => 100,
                'visible' => true,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'option' => [
                    'values' => [
                        'Beginner',
                        'Enthusiast',
                        'Professional'
                    ]
                ]
            ]
        ];

        foreach ($attributesData as $code => $attrData) {
            $eavSetup->addAttribute($entityTypeId, $code, $attrData);
            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $idGroup,
                $code,
                $attrData['sort_order']
            );
        }
    }
}
