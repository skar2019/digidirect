<?php

namespace Ewave\Navigation\Ui\DataProvider\Menu\Form\Modifier;

use Ewave\Navigation\Api\Data\MenuItemInterface;
use Ewave\Navigation\Api\Data\SetInterface;
use Ewave\Navigation\Model\Menu;
use Magento\Framework\Registry;
use Ewave\Navigation\Model\ResourceModel\Menu\Grid\CollectionFactory as MenuCollectionFactory;
use Ewave\Navigation\Model\ResourceModel\Set\Grid\CollectionFactory as SetCollectionFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class MenuItems extends MenuModifier
{
    /**
     * @var MenuCollectionFactory
     */
    protected $menuCollectionFactory;

    /**
     * @var SetCollectionFactory
     */
    protected $setCollectionFactory;

    /**
     * @var array
     */
    protected $menuItemsTree = [];

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $storeById = [];

    /**
     * MenuItems constructor.
     *
     * @param Registry $registry
     * @param MenuCollectionFactory $menuCollectionFactory
     * @param SetCollectionFactory $setCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        MenuCollectionFactory $menuCollectionFactory,
        SetCollectionFactory $setCollectionFactory,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($registry, $data);
        $this->menuCollectionFactory = $menuCollectionFactory;
        $this->setCollectionFactory = $setCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Modify meta - add menu items dropdown ui select
     *
     * @param [] $meta
     * @return []
     */
    public function modifyMeta(array $meta)
    {
        $meta[self::MENU_ITEM_INFORMATION_DATASCOPE]['children']['parent_menu_item_id'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Parent Menu Item'),
                        'formElement' => 'select',
                        'componentType' => 'field',
                        'component' => 'Ewave_Navigation/js/component/tree-dropdown',
                        'filterOptions' => true,
                        'chipsEnabled' => true,
                        'disableLabel' => true,
                        'levelsVisibility' => '1',
                        'elementTmpl' => 'Ewave_Navigation/grid/filters/elements/ui-select',
                        'options' => $this->_getMenuTree(),
                        'scopeLabel' => __('[Store View]'),
                        'config' => [
                            'dataScope' => self::MENU_ITEM_INFORMATION_DATASCOPE,
                            'sortOrder' => 40,
                        ],
                    ],
                ],
            ],
        ];
        return $meta;
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
     * Get menu items as tree
     * do not select current menu item - obviously we can not specify itself as parent
     *
     * @return []
     */
    protected function _getMenuTree()
    {
        $currentMenuItemId = $this->_getCurrentMenuItem()->getId();
        if (isset($this->menuItemsTree[$currentMenuItemId])) {
            return $this->menuItemsTree[$currentMenuItemId];
        }

        /**
         * @var $collection \Ewave\Navigation\Model\ResourceModel\Menu\Grid\Collection
         */
        $collection = $this->menuCollectionFactory->create();
        $setCollection = $this->setCollectionFactory->create();

        $collection->setCurrentStoreId($this->_getCurrentStoreId());
        if ($currentMenuItemId) {
            $collection->addFieldToFilter(
                'parent_menu_item_id',
                ['neq' => $currentMenuItemId]
            );
            $collection->addFieldToFilter(
                'id',
                ['neq' => $currentMenuItemId]
            );
        }

        $defaultParent = 0;
        $menuById = [
            $defaultParent => [
                'value' => 'No parent',
                'optgroup' => null,
            ],
        ];

        /**
         * @var $set SetInterface
         */
        foreach ($setCollection->getItems() as $set) {
            $menuById['set' . $set->getId()]['value'] = 'set' . $set->getId();
            $menuById['set' . $set->getId()]['is_active'] = 'true';
            $menuById['set' . $set->getId()]['label'] = $set->getName();
            $menuById['set' . $set->getId()]['is_nonclickable'] = true;
            $menuById[0]['optgroup'][] = &$menuById['set' . $set->getId()];
        }

        /**
         * @var $menuItem Menu
         */
        foreach ($collection->getItems() as $menuItem) {
            $pathAsArray = explode('/', $menuItem->getPath());
            if (in_array($currentMenuItemId, $pathAsArray)) {
                continue;
            }
            foreach ([$menuItem->getId(), $menuItem->getParentMenuItemId()] as $menuId) {
                if (!isset($menuById[$menuId])) {
                    $menuById[$menuId] = ['value' => $menuId];
                }
            }

            $menuById[$menuItem->getId()]['is_active'] = $menuItem->getStatus();
            $menuString = ' (code: ' . $menuItem->getMenuItemCode();
            $menuString .= ' | stores: ' . $this->prepareStoresString($menuItem->getStoreId()) .  ')';
            $menuById[$menuItem->getId()]['label'] = $menuItem->getTitle() . $menuString;
            if ($menuItem->getParentMenuItemId() > 0) {
                $menuById[$menuItem->getParentMenuItemId()]['optgroup'][] = &$menuById[$menuItem->getId()];
            } else {
                $menuById['set' . $menuItem->getMenuSetId()]['optgroup'][] = &$menuById[$menuItem->getId()];
            }
        }

        $menuById[$defaultParent]['optgroup']['empty'] = [
            'label' => __('No parent item'),
            'value' => '0',
        ];
        $this->menuItemsTree[$currentMenuItemId] = $menuById[$defaultParent]['optgroup'];

        return $this->menuItemsTree[$currentMenuItemId];
    }

    /**
     * @param int $id
     * @return string
     */
    protected function prepareStoresString($id)
    {
        if (!is_array($id)) {
            $id = [$id];
        }

        $stores = [];
        foreach ($id as $identifier) {
            if (0 == $identifier) {
                $storeName = 'All Store Views';
            } else {
                $storeName = $this->getStore($identifier)->getName();
            }
            $stores[] = $storeName;
        }

        return implode(', ', $stores);
    }

    /**
     * @param int $id
     * @return \Magento\Store\Model\Store
     */
    protected function getStore($id)
    {
        try {
            if (!isset($this->storeById[$id])) {
                $this->storeById[$id] = $this->storeManager->getStore($id);
            }

            return $this->storeById[$id];
        } catch (\Throwable $exception) {
            return $this->storeManager->getDefaultStoreView();
        }
    }
}
