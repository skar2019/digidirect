<?php

namespace Ewave\ProntoDigi\Model\ResourceModel;

use Ewave\ProntoDigi\ProntoApi\Constants\Products as ProductsConst;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Indexer\Category\Product\TableMaintainer;
use Magento\Catalog\Model\ResourceModel\Category;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Ewave\ProntoDigi\ProntoApi\Constants\InventoryGetRequest;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

/**
 * Class Product
 * @package Ewave\ProntoDigi\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends \Magento\Catalog\Model\ResourceModel\Product
{
    const DEFAULT_BUNCH_SIZE = 1000;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Product constructor.
     * @param \Magento\Eav\Model\Entity\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Factory $modelFactory
     * @param Category\CollectionFactory $categoryCollectionFactory
     * @param Category $catalogCategory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
     * @param \Magento\Eav\Model\Entity\TypeFactory $typeFactory
     * @param \Magento\Catalog\Model\Product\Attribute\DefaultAttributes $defaultAttributes
     * @param CollectionFactory $collectionFactory
     * @param array $data
     * @param TableMaintainer|null $tableMaintainer
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Factory $modelFactory,
        Category\CollectionFactory $categoryCollectionFactory,
        Category $catalogCategory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Magento\Eav\Model\Entity\TypeFactory $typeFactory,
        \Magento\Catalog\Model\Product\Attribute\DefaultAttributes $defaultAttributes,
        CollectionFactory $collectionFactory,
        array $data = [],
        TableMaintainer $tableMaintainer = null
    ) {
        parent::__construct(
            $context,
            $storeManager,
            $modelFactory,
            $categoryCollectionFactory,
            $catalogCategory,
            $eventManager,
            $setFactory,
            $typeFactory,
            $defaultAttributes,
            $data,
            $tableMaintainer
        );

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function getAttributeSetList()
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->getTable('eav_attribute_set'), 'attribute_set_name')
            ->where('entity_type_id = ?', $this->getTypeId());

        return $connection->fetchCol($select);
    }

    /**
     * @return array
     */
    public function getExistSkus()
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from(['p' => $this->getEntityTable()], [ProductInterface::SKU, ProductInterface::TYPE_ID]);

        $this->addOptionToSelect($select, ProductsConst::PRODUCT_ATTRIBUTE_STOCK_STATUS);
        $this->addOptionToSelect($select, ProductsConst::PRODUCT_ATTRIBUTE_STOCK_CONDITION);

        return $connection->fetchAssoc($select);
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param string $attrCode
     */
    protected function addOptionToSelect(\Magento\Framework\DB\Select $select, $attrCode)
    {
        $connection = $this->getConnection();
        $stockStatusAttribute = $this->getAttribute($attrCode);
        $linkFiled = $this->getLinkField();
        $select->joinLeft(
            [$attrCode => $stockStatusAttribute->getBackendTable()],
            implode(' AND ', [
                $attrCode . '.' . $linkFiled . ' = p.' . $linkFiled,
                $connection->quoteInto($attrCode . '.attribute_id = ?', $stockStatusAttribute->getId()),
                $connection->quoteInto($attrCode . '.store_id = ?', Store::DEFAULT_STORE_ID),
            ]),
            []
        )->joinLeft(
            [$attrCode . 'option_value' => $this->getTable('eav_attribute_option_value')],
            implode(' AND ', [
                $attrCode . 'option_value.option_id = ' . $attrCode . '.value',
                $connection->quoteInto($attrCode . '.store_id = ?', Store::DEFAULT_STORE_ID),
            ]),
            [$attrCode => $attrCode . 'option_value.value']
        );
    }

    /**
     * @return array
     */
    public function getExcludedSkus()
    {
        $excludeAttribute = $this->getAttribute(ProductsConst::PRODUCT_ATTRIBUTE_EXCLUDE_FROM_INTEGRATION);
        $linkFiled = $this->getLinkField();
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from(['p' => $this->getEntityTable()], [ProductInterface::SKU])
            ->join(
                ['at_excl' => $excludeAttribute->getBackendTable()],
                implode(' AND ', [
                    'at_excl.' . $linkFiled . ' = p.' . $linkFiled,
                    $connection->quoteInto('at_excl.attribute_id = ?', $excludeAttribute->getId()),
                    $connection->quoteInto('at_excl.store_id = ?', Store::DEFAULT_STORE_ID),
                ]),
                [ProductsConst::PRODUCT_ATTRIBUTE_EXCLUDE_FROM_INTEGRATION => 'at_excl.value']
            )
            ->where('at_excl.value = 1');
        return $connection->fetchPairs($select);
    }

    /**
     * @param int $status
     * @return array
     */
    public function getExistSkusByStatus($status = ProductStatus::STATUS_ENABLED)
    {
        $statusAttribute = $this->getAttribute(ProductInterface::STATUS);
        $linkFiled = $this->getLinkField();
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from(['p' => $this->getEntityTable()], [ProductInterface::SKU, ProductInterface::TYPE_ID])
            ->join(
                ['at_status' => $statusAttribute->getBackendTable()],
                implode(' AND ', [
                    'at_status.' . $linkFiled . ' = p.' . $linkFiled,
                    $connection->quoteInto('at_status.attribute_id = ?', $statusAttribute->getId()),
                    $connection->quoteInto('at_status.store_id = ?', Store::DEFAULT_STORE_ID),
                ]),
                []
            )
            ->where('at_status.value = ?', $status);
        return $connection->fetchPairs($select);
    }

    /**
     * @param array $products
     * @param array $attributes
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateProductAttributes($products, $attributes = [])
    {
        $linkFiled = $this->getLinkField();
        $productCollection = $this->collectionFactory->create();
        foreach ($attributes as $code) {
            $productCollection->addAttributeToSelect($code, 'left');
        }
        $productCollection->addAttributeToFilter('sku', ['in' => array_keys($products)]);
        $select = $productCollection->getSelect();
        $items = $this->getConnection()->fetchAll($select);

        foreach (array_chunk($items, self::DEFAULT_BUNCH_SIZE) as $itemsBunch) {
            $dataToUpdate = [];
            foreach ($itemsBunch as $item) {
                foreach ($attributes as $attributeCode) {
                    $productData = $products[$item['sku']];
                    if (
                        isset($productData[$attributeCode]) &&
                        $productData[$attributeCode] != $item[$attributeCode]
                    ) {
                        $attribute = $this->getAttribute($attributeCode);
                        $attributeTable = $attribute->getBackendTable();
                        $dataToUpdate[$attributeTable][] = [
                            $linkFiled => $item[$linkFiled],
                            'attribute_id' => $attribute->getAttributeId(),
                            'store_id' => Store::DEFAULT_STORE_ID,
                            'value' => $productData[$attributeCode]
                        ];
                    }
                }
            }
            foreach ($dataToUpdate as $tableName => $data) {
                $this->getConnection()->insertOnDuplicate(
                    $tableName,
                    $data,
                    ['value']
                );
            }
        }
    }
}
