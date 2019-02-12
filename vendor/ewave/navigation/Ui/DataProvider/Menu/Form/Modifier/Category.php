<?php

namespace Ewave\Navigation\Ui\DataProvider\Menu\Form\Modifier;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Category extends MenuModifier
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var array
     */
    protected $categoriesTree = [];

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Category constructor.
     *
     * @param Registry $registry
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        array $data
    ) {
        parent::__construct($registry, $data);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Modify data implemented method
     *
     * @param [] $data
     * @return []
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify meta - add categories dropdown
     *
     * @param [] $meta
     * @return []
     */
    public function modifyMeta(array $meta)
    {
        $meta[self::MENU_ITEM_INFORMATION_DATASCOPE]['children']['category_id'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Category'),
                        'formElement' => 'select',
                        'componentType' => 'field',
                        'component' => 'Ewave_Navigation/js/component/tree-dropdown',
                        'filterOptions' => true,
                        'chipsEnabled' => true,
                        'disableLabel' => true,
                        'levelsVisibility' => '1',
                        'elementTmpl' => 'Ewave_Navigation/grid/filters/elements/ui-select',
                        'options' => $this->getCategoriesTree(),
                        'scopeLabel' => __('[Store View]'),
                        'config' => [
                            'dataScope' => self::MENU_ITEM_INFORMATION_DATASCOPE,
                            'sortOrder' => 40,
                        ],
                        'validation' => [
                            'required-entry' => true,
                        ],
                    ],
                ],
            ],
        ];
        return $meta;
    }

    /**
     * Get categories tree
     *
     * @return []
     */
    protected function getCategoriesTree()
    {
        if (!empty($this->categoriesTree[0])) {
            return $this->categoriesTree[0];
        }

        $storeId = $this->_getCurrentStoreId();
        $defaultRootCategory = CategoryModel::ROOT_CATEGORY_ID;

        /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $collection = $this->categoryCollectionFactory->create();

        $collection
            ->addAttributeToSelect(['name', 'is_active', 'parent_id'])
            ->setStoreId($storeId);

        $categoryById[$defaultRootCategory] = [
            'value' => $defaultRootCategory,
            'optgroup' => null,
        ];

        $rootCategories = [];

        /**
         * @var $category \Magento\Catalog\Model\Category
         */
        foreach ($collection as $category) {
            if (!$this->isInRootCategoryList($category)) {
                $rootCategories[] = $category->getId();
            }
            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = ['value' => $categoryId];

                    if (in_array($category->getId(), $rootCategories)) {
                        $categoryById[$category->getId()]['is_nonclickable'] = true;
                    }
                }
            }
            $categoryById[$category->getId()]['label'] = $category->getName();
            $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];
        }

        $this->categoriesTree[0] = $categoryById[$defaultRootCategory]['optgroup'];

        return $this->categoriesTree[0];
    }

    /**
     * @param CategoryModel $category
     * @return bool
     */
    protected function isInRootCategoryList(\Magento\Catalog\Model\Category $category)
    {
        try {
            if($this->_getCurrentStoreId() == Store::DEFAULT_STORE_ID) {
                $rootCat = $this->storeManager->getDefaultStoreView()->getRootCategoryId();
            } else {
                $rootCat = $this->storeManager->getStore($this->_getCurrentStoreId())->getRootCategoryId();
            }
            return in_array($rootCat, $category->getParentIds());
        } catch (\Throwable $exception) {
            return $category->isInRootCategoryList();
        }
    }
}
