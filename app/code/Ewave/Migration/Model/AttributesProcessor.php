<?php
namespace Ewave\Migration\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\GroupFactory as AttributeGroupFactory;

/**
 * Class AttributesProcessor
 * @package Ewave\Migration\Model
 */
class AttributesProcessor
{
    /**
     * @var \Ewave\Migration\Helper\Data
     */
    protected $helper;

    /**
     * @var \Ewave\Migration\Helper\Attributes
     */
    protected $attrHelper;

    protected $optionIds = [];

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_filesystemDriver;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $_productAttributeRepository;

    /**
     * @var EavSetup
     */
    protected $eavSetup;

    /**
     * @var AttributeSetFactory
     */
    protected $attributeSetFactory;

    /**
     * @var AttributeGroupFactory
     */
    protected $attributeGroupFactory;

    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /**
     * AttributesProcessor constructor.
     * @param \Ewave\Migration\Helper\Data $helper
     * @param \Ewave\Migration\Helper\Attributes $attrHelper
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository
     * @param AttributeFactory $attributeFactory
     * @param \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     * @param AttributeSetFactory $attributeSetFactory
     * @param AttributeGroupFactory $attributeGroupFactory
     * @param ProductHelper $productHelper
     * @param EavSetup $eavSetup
     */
    public function __construct(
        \Ewave\Migration\Helper\Data $helper,
        \Ewave\Migration\Helper\Attributes $attrHelper,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        AttributeFactory $attributeFactory,
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver,
        AttributeSetFactory $attributeSetFactory,
        AttributeGroupFactory $attributeGroupFactory,
        ProductHelper $productHelper,
        EavSetup $eavSetup
    ) {
        $this->helper = $helper;
        $this->attrHelper = $attrHelper;
        $this->_filesystemDriver = $filesystemDriver;
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->attributeFactory = $attributeFactory;
        $this->eavSetup = $eavSetup;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->productHelper = $productHelper;
    }

    /**
     * @param $data
     * @param $output
     * @throws LocalizedException
     */
    public function process($data, $output)
    {
        $output->writeln('Starting an attribute migration process: ');
        $errorsIds = [];

        $entityTypeId = $this->eavSetup->getEntityTypeId(Product::ENTITY);

        foreach ($data as $row) {
            $row = $this->attrHelper->prepareRowValues($row);

            try {
                $attribute = $this->_productAttributeRepository->get($row['attribute_code']);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $attribute = $this->attributeFactory->create(['entity_type_id' => $entityTypeId]);
            }

            try {
                $attributeSetId = $this->processAttributeSet($row['attribute_set']);
            } catch (LocalizedException $e) {
                $attributeSetId = 0;
            } finally {
                unset($row['attribute_set']);
            }

            try {
                $attributeGroupId = $this->processAttributeGroup($attributeSetId, $row['attribute_group']);
            } catch (LocalizedException $e) {
                $attributeGroupId = 0;
            } finally {
                unset($row['attribute_group']);
            }

            if ($attribute->getId()) {
                // entity type check
                if ($attribute->getEntityTypeId() != $entityTypeId) {
                    throw new \Exception("Incorrect entity type for attribute {$row['attribute_code']}");
                }

                if ($attribute->getFrontendInput() != $row['frontend_input']) {
                    unset($row['frontend_input']);
                }

                $this->attrHelper->processDuplicateOptions($attribute, $row);
            }

            $attribute->addData($row);

            try {
                $this->_productAttributeRepository->save($attribute);

                if ($attributeSetId && $attributeGroupId) {
                    $this->eavSetup->addAttributeToGroup(
                        $entityTypeId,
                        $attributeSetId,
                        $attributeGroupId,
                        $attribute->getAttributeCode(),
                        (int) $attribute->getPosition()
                    );
                }

                $output->write('.');
            } catch (\Exception $e) {
                $output->write('x');
                $errorsIds[$attribute->getAttributeCode()] = $e->getMessage();
            }
        }

        if (!empty($errorsIds)) {
            $output->writeln('');
            $output->writeln('Errors: ');

            foreach ($errorsIds as $key => $error) {
                $output->writeln('ID: ' . $key . ' - ' . $error);
            }
        }
    }

    /**
     * @param string $attributeCode
     * @param string $label
     * @param ProductInterface $product
     * @return int
     */
    protected function getOptionIdByLabel(string $attributeCode, string $label, ProductInterface $product):int
    {
        if (!isset($this->optionIds[$attributeCode][$label])) {
            $optionId = $this->helper->getAttributeOptionId($attributeCode, $label, $product);
            if (!$optionId) {
                $optionId = $this->helper->addAttributeOptionId($attributeCode, $label, $product);
            }
            $this->optionIds[$attributeCode][$label] = $optionId;
        }

        return $this->optionIds[$attributeCode][$label];
    }

    /**
     * @param string|null $name
     * @return int
     * @throws LocalizedException
     */
    protected function processAttributeSet(?string $name):int
    {
        if (!$name) return 0;

        $entityTypeId = $this->eavSetup->getEntityTypeId(Product::ENTITY);
        $defaultSetId = $this->eavSetup->getDefaultAttributeSetId($entityTypeId);

        $attributeSet = $this->attributeSetFactory->create();
        $setCollection = $attributeSet->getResourceCollection()
            ->addFieldToFilter('entity_type_id', $entityTypeId)
            ->addFieldToFilter('attribute_set_name', $name)
            ->load();
        $attributeSet = $setCollection->fetchItem();

        if (!$attributeSet) {
            $attributeSet = $this->attributeSetFactory->create();
            $attributeSet->setEntityTypeId($entityTypeId);
            $attributeSet->setAttributeSetName($name);
            $attributeSet->setSortOrder($this->eavSetup->getAttributeSetSortOrder($entityTypeId));
            $attributeSet->save();
            $attributeSet->initFromSkeleton($defaultSetId);
            $attributeSet->save();
        }

        return $attributeSet->getId();
    }

    /**
     * @param int|null $setId
     * @param string|null $name
     * @return int
     * @throws LocalizedException
     */
    protected function processAttributeGroup(?int $setId, ?string $name):int
    {
        if (!$setId) return 0;
        if (!$name) $name = 'Product Details';

        $entityTypeId = $this->eavSetup->getEntityTypeId(Product::ENTITY);

        $code = $this->eavSetup->convertToAttributeGroupCode($name);
        $name = htmlspecialchars($name);

        $attributeGroup = $this->attributeGroupFactory->create();
        $setCollection = $attributeGroup->getResourceCollection()
            ->addFieldToFilter('attribute_set_id', $setId)
            ->addFieldToFilter('attribute_group_code', $code)
            ->load();
        $attributeGroup = $setCollection->fetchItem();

        if (!$attributeGroup) {
            $attributeGroup = $this->attributeGroupFactory->create();
            $attributeGroup->setAttributeSetId($setId);
            $attributeGroup->setAttributeGroupName($name);
            $attributeGroup->setAttributeGroupCode($code);
            $attributeGroup->setSortOrder($this->eavSetup->getAttributeGroupSortOrder($entityTypeId, $setId));
            $attributeGroup->save();
        }

        return $attributeGroup->getId();
    }
}
