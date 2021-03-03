<?php

namespace Digidirect\Navigation\Model\Menu\Type;

use Digidirect\Navigation\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;

class Category extends AbstractType implements MenuDataInterface
{
    const PREFIX = 'cat';

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var LayerResolver
     */
    protected $layerResolver;

    /**
     * @var null|int
     */
    protected $currentCategoryId = null;

    /**
     * @var null|[]
     */
    protected $activeCategories = null;

    /**
     * Category constructor.
     *
     * @param Data $helper
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param LayerResolver $layerResolver
     */
    public function __construct(
        Data $helper,
        CategoryCollectionFactory $categoryCollectionFactory,
        LayerResolver $layerResolver
    ) {
        parent::__construct($helper);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->layerResolver = $layerResolver;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $category = $this->_getActiveCategoryById($this->item->getCategoryId());
        return $category ? $category->getUrl() : '';
    }

    /**
     * @return []
     */
    public function getMenuData()
    {
        $array = [
            'is_link' => true,
        ];

        if ($this->item->getUseCategoryHierarchy()) {
            $menuCategoryId = $this->item->getCategoryId();
            $category = $this->_getActiveCategoryById($menuCategoryId);
            $nestingLevel = $this->item->getNestingLevel();
            if ($category && $nestingLevel && $category->hasChildren()) {
                $menuId = $this->item->getId();
                $parentCategory = [$category->getId() => $menuId];
                $menuByIdArray = [
                    $menuId => [
                        'children' => [],
                    ],
                ];

                $arrayKeys = array_keys($this->_getActiveCategories());

                $children = $category->getAllChildren(true);

                $sortedChildren = [];
                foreach ($children as $child) {
                    $key = array_search($child, $arrayKeys);
                    $sortedChildren[$key] = $child;
                }
                ksort($sortedChildren);

                foreach ($sortedChildren as $categoryId) {
                    $cat = $this->_getActiveCategoryById($categoryId);
                    if (!$cat || !$cat->getIncludeInMenu()) {
                        continue;
                    }
                    $parentItemId = $parentCategory[$cat->getParentId()] ?? self::PREFIX . $cat->getParentId();
                    if (!isset($menuByIdArray[$parentItemId])) {
                        continue;
                    }

                    $categoryRelativeLevel = $cat->getLevel() - $category->getLevel();
                    if ($categoryRelativeLevel > $nestingLevel) {
                        continue;
                    }

                    foreach ([self::PREFIX . $cat->getId(), $parentItemId] as $categoryItemId) {
                        if (!isset($menuByIdArray[$categoryItemId])) {
                            $menuByIdArray[$categoryItemId] = ['value' => $categoryItemId];
                        }
                    }

                    $menuByIdArray[self::PREFIX . $cat->getId()] = $this->getCategoryMenuItemData($cat);
                    $menuByIdArray[$parentItemId]['children'][] = &$menuByIdArray[self::PREFIX . $cat->getId()];
                }

                $menuArray = $menuByIdArray[$menuId]['children'];
                $array['children'] = $menuArray;
            }
        }
        return $array;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return []
     */
    public function getCategoryMenuItemData(\Magento\Catalog\Model\Category $category)
    {
        if (!$this->currentCategoryId) {
            $currentCategory = $this->layerResolver->get();
            if ($currentCategory) {
                $this->currentCategoryId = $currentCategory->getCurrentCategory()->getId();
            }
        }
        $isActive = $this->currentCategoryId == $category->getId();

        $categoryMenuItem = [
            'url' => $category->getUrl(),
            'position' => $category->getPosition(),
            'title' => $category->getName(),
            'identifier' => 'menu-node-category' . $category->getId(),
            'is_link' => true,
            'is_active' => $isActive,
            'custom_options' => [],
        ];

        return $categoryMenuItem;
    }

    /**
     * Get category by id
     *
     * @param int $id
     * @return \Magento\Catalog\Model\Category|null
     */
    protected function _getActiveCategoryById($id)
    {
        $categories = $this->_getActiveCategories();
        return $categories[$id] ?? null;
    }

    /**
     * First of all check if it can be displayed at all
     * Then check category
     *
     * @return bool
     */
    public function isAvailable()
    {
        $isAvailable = parent::isAvailable();
        if (false === $isAvailable) {
            return $isAvailable;
        }
        $activeCategories = $this->_getActiveCategories();
        return isset($activeCategories[$this->item->getCategoryId()]);
    }

    /**
     * Get active categories array
     *
     * @return \Magento\Catalog\Model\Category[]
     */
    protected function _getActiveCategories()
    {
        if (null === $this->activeCategories) {
            $categories = [];
            /**
             * @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection
             */
            $collection = $this->categoryCollectionFactory->create();
            $collection->addFieldToFilter('is_active', ['eq' => true]);
            $collection->addAttributeToSelect('name');
            $collection->addAttributeToSelect('include_in_menu');
            $collection->addUrlRewriteToResult();
            $collection->addOrder('level', Collection::SORT_ORDER_ASC);
            $collection->addOrder('position', Collection::SORT_ORDER_ASC);
            $collection->addOrder('parent_id', Collection::SORT_ORDER_ASC);
            $collection->addOrder('entity_id', Collection::SORT_ORDER_ASC);
            foreach ($collection as $category) {
                $categories[$category->getId()] = $category;
            }
            $this->activeCategories = $categories;
        }
        return $this->activeCategories;
    }
}
