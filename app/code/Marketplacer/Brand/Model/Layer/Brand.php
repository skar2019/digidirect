<?php

namespace Marketplacer\Brand\Model\Layer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Category;
use Magento\Catalog\Model\Layer\ContextInterface;
use Magento\Catalog\Model\Layer\StateFactory;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;

class Brand extends Category
{
    const LAYER_NAME = 'brand_category_layer';

    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriever;

    /**
     * @param ContextInterface $context
     * @param StateFactory $layerStateFactory
     * @param CollectionFactory $attributeCollectionFactory
     * @param Product $catalogProduct
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param BrandAttributeRetrieverInterface $brandAttributeRetriever
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StateFactory $layerStateFactory,
        CollectionFactory $attributeCollectionFactory,
        Product $catalogProduct,
        StoreManagerInterface $storeManager,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        BrandAttributeRetrieverInterface $brandAttributeRetriever,
        array $data = []
    ) {
        parent::__construct($context, $layerStateFactory, $attributeCollectionFactory, $catalogProduct, $storeManager,
            $registry, $categoryRepository, $data);

        $this->brandAttributeRetriever = $brandAttributeRetriever;
    }

    /**
     * Retrieve current layer product collection
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getProductCollection()
    {
        $brand = $this->getCurrentBrand();
        if (isset($this->_productCollections[$brand->getOptionId()])) {
            $collection = $this->_productCollections[$brand->getOptionId()];
        } else {
            $collection = $this->collectionProvider
                ->getCollection($this->getCurrentCategory())
                ->addFieldToFilter($this->brandAttributeRetriever->getAttributeCode(), $brand->getOptionId());

            $this->prepareProductCollection($collection);
            $this->_productCollections[$brand->getOptionId()] = $collection;
        }

        return $collection;
    }

    /**
     * @return BrandInterface
     */
    public function getCurrentBrand()
    {
        $currentBrand = $this->_getData(self::LAYER_NAME);
        if ($currentBrand === null && ($currentBrand = $this->registry->registry('current_brand'))) {
            $this->setData(self::LAYER_NAME, $currentBrand);
        }
        return $currentBrand;
    }
}
