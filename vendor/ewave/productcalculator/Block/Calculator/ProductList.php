<?php

namespace Ewave\ProductCalculator\Block\Calculator;

use Ewave\ProductCalculator\Model\Calculator\Result as CalculatorResult;
use Ewave\ProductCalculator\Model\Calculator;
use Ewave\ProductCalculator\Model\Constants;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Url\Helper\Data;

/**
 * Class ProductList
 *
 * @package Ewave\ProductCalculator\Block\Calculator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductList extends ListProduct
{
    const NAME_IN_LAYOUT = 'calculator_product_list_wrapper';

    /**
     * Product collection factory
     *
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var CalculatorResult
     */
    protected $calculatorResult;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var array
     */
    protected $groupedByCategoryProducts;

    /**
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param CollectionFactory $productCollectionFactory
     * @param CalculatorResult $calculatorResult
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        CollectionFactory $productCollectionFactory,
        CalculatorResult $calculatorResult,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->calculatorResult = $calculatorResult;
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * @return Collection
     * @throws LocalizedException
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $collection = $this->getPreparedCollection();

            $collection->load();
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    /**
     * @param Collection $collection
     * @param Calculator $calculator
     * @return array
     */
    public function groupProductsByCategory($collection, $calculator = null)
    {
        if ($calculator === null) {
            $calculator = $this->getCurrentCalculator();
        }
        $inputData = $this->getRequest()->getParam('input_groups');
        $categories = $this->getCategoriesFromProductCollection($collection);
        $categoriesToShow = $this->calculatorResult->getCategoriesToShowIds($inputData);
        $groupedProducts = [];
        $productPositions = [];
        $sortedProducts = [];

        foreach ($collection as $product) {
            foreach ($product->getCategoryIds() as $categoryId) {
                if (!$calculator->getProductsCount()) {
                    break 2;
                }

                if (!isset($categories[$categoryId]) or !in_array($categoryId, $categoriesToShow)) {
                    continue;
                }
                
                /** @var CategoryInterface|Category $category */
                $category = $categories[$categoryId];
                $categoryName = $category->getName();

                if (!isset($productPositions[$categoryId])) {
                    $productPositions[$categoryId] = $category->getProductsPosition();
                }

                $position = $productPositions[$categoryId][$product->getId()] ?? null;
                if ($position !== null && !isset($sortedProducts[$categoryName][$position])) {
                    $sortedProducts[$categoryName][$position] = $product;
                } else {
                    $sortedProducts[$categoryName][] = $product;
                }
                ksort($sortedProducts[$categoryName]);
            }
        }

        $gridLimit = $calculator->getProductsCount();
        foreach ($sortedProducts as $categoryName => $groupedProduct) {
            if ($gridLimit <= 0) {
                break;
            }
            $categoryLimit = $calculator->getProductsCountForCategory();
            $groupedProducts[$categoryName] = array_slice(
                $groupedProduct,
                0,
                $gridLimit < $categoryLimit ? $gridLimit : $categoryLimit
            );
            $gridLimit = $gridLimit - count($groupedProducts[$categoryName]);
        }

        $categoryPositions = $this->getCategoriesPositions($groupedProducts);
        $groupedProducts = array_replace(array_flip($categoryPositions), $groupedProducts);
        return $groupedProducts;
    }

    /**
     * @param array $groupedProducts
     * @return array
     * @throws LocalizedException
     */
    protected function getCategoriesPositions(array $groupedProducts)
    {
        $resultSort = [];
        $categories = array_keys($groupedProducts);
        if (!empty($categories)) {
            /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
            $categoryCollection = $this->categoryCollectionFactory->create();
            $categoriesArray = $categoryCollection
                ->addAttributeToSelect('name')
                ->load()
                ->toArray();

            $sortedCategories = $this->getSortedCategories($categoriesArray);
            foreach ($categories as $categoryName) {
                $position = array_search($categoryName, $sortedCategories);
                if ($position !== false) {
                    $resultSort[$position] = $categoryName;
                }
            }
            ksort($resultSort);
        }

        return $resultSort;
    }

    /**
     * @param array $categories
     * @return array
     */
    protected function getSortedCategories(array $categories)
    {
        $tree = $grouped = $result = [];
        foreach ($categories as $id => &$node) {
            if (!$node['parent_id']) {
                $tree[$id] = &$node;
            } else {
                $categories[$node['parent_id']]['childs'][$id] = &$node;
            }
        }
        $this->sortCategoriesTree($tree, $grouped);
        $this->buildCategoriesSortedList($grouped, $result);

        return $result;
    }

    /**
     * @param array $array
     * @param array $result
     * @return array
     */
    protected function sortCategoriesTree(array $array, array &$result)
    {
        foreach ($array as $item) {
            $result[$item['position']] = ['name' => $item['name']];
            if (!empty($item['childs'])) {
                if (!isset($result[$item['position']]['childs'])) {
                    $result[$item['position']]['childs'] = [];
                }
                $this->sortCategoriesTree($item['childs'], $result[$item['position']]['childs']);
            }
            ksort($result);
        }
        return $result;
    }

    /**
     * @param array $array
     * @param array $result
     * @return array
     */
    protected function buildCategoriesSortedList(array $array, array &$result)
    {
        foreach ($array as $item) {
            $result[] = $item['name'];
            if (!empty($item['childs'])) {
                $this->buildCategoriesSortedList($item['childs'], $result);
            }
        }
        return $result;
    }

    /**
     * @return Collection
     * @throws LocalizedException
     */
    protected function getPreparedCollection()
    {
        /** @var Calculator $calculator */
        $calculator = $this->_coreRegistry->registry(Constants::CURRENT_CALCULATOR);
        $inputData = $this->getRequest()->getParam('input_groups');
        if ($calculator && $inputData) {
            $productIds = $this->calculatorResult->calculate($calculator, $inputData);
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->productCollectionFactory->create()
                ->addAttributeToSelect(
                    $this->_catalogConfig->getProductAttributes()
                )
                ->addStoreFilter();
            $collection->addIdFilter($productIds);
            $categoriesToShow = $this->calculatorResult->getCategoriesToShowIds($inputData);
            if ($this->getCurrentCalculator()->getSeparateProductsByCategories() && $categoriesToShow) {
                $collection->addCategoriesFilter(['in' => $categoriesToShow]);
            }
            $this->_catalogLayer->prepareProductCollection($collection);

            return $collection;
        } else {
            throw new LocalizedException(__('Invalid product finder data'));
        }
    }

    /**
     * @return Calculator
     */
    public function getCurrentCalculator()
    {
        return $this->_coreRegistry->registry(Constants::CURRENT_CALCULATOR);
    }

    /**
     * @param Collection $collection
     * @return array
     */
    protected function getCategoriesFromProductCollection($collection)
    {
        $categoryIds = [];
        foreach ($collection as $product) {
            $categoryIds = array_merge($categoryIds, $product->getCategoryIds());
        }
        $categoryIds = array_unique($categoryIds);

        $categoryCollection = $this->categoryCollectionFactory->create();
        $categories = $categoryCollection
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('is_active', 1)
            ->addIdFilter($categoryIds)
            ->getItems();

        return $categories;
    }

    /**
     * @return array
     */
    public function getGroupedByCategoryProducts()
    {
        if ($this->groupedByCategoryProducts !== null) {
            return $this->groupedByCategoryProducts;
        }
        if (!$this->getCurrentCalculator()->getSeparateProductsByCategories()) {
            $groupedByCategoryProducts = $this->getLoadedProductCollection()->getItems();
            $groupedByCategoryProducts = array_slice(
                $groupedByCategoryProducts,
                0,
                $this->getCurrentCalculator()->getProductsCount()
            );
            $this->groupedByCategoryProducts = $groupedByCategoryProducts ? ['' => $groupedByCategoryProducts] : [];
        } else {
            $this->groupedByCategoryProducts = $this->groupProductsByCategory(
                $this->getPreparedCollection(),
                $this->getCurrentCalculator()
            );
        }

        return $this->groupedByCategoryProducts;
    }
}
