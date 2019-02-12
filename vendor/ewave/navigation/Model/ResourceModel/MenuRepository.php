<?php

namespace Ewave\Navigation\Model\ResourceModel;

use Ewave\Navigation\Model\Menu as MenuModel;
use Ewave\Navigation\Model\ResourceModel\Menu\CollectionFactory as MenuCollectionFactory;
use Ewave\Navigation\Api\MenuRepositoryInterface;
use Ewave\Navigation\Model\MenuFactory;

/**
 * Class MenuRepository
 *
 */
class MenuRepository implements MenuRepositoryInterface
{
    /**
     * @var \Ewave\navigation\Model\MenuFactory
     */
    protected $menuFactory;

    /**
     * @var Menu
     */
    protected $menuResource;

    /**
     * @var array
     */
    protected $menuItemsBySet = [];

    /**
     * @var MenuCollectionFactory
     */
    protected $menuCollectionFactory;

    /**
     * MenuRepository constructor.
     *
     * @param MenuFactory $menuFactory
     * @param Menu $menuResource
     * @param MenuCollectionFactory $collectionFactory
     */
    public function __construct(
        MenuFactory $menuFactory,
        Menu $menuResource,
        MenuCollectionFactory $collectionFactory
    ) {
        $this->menuCollectionFactory = $collectionFactory;
        $this->menuFactory = $menuFactory;
        $this->menuResource = $menuResource;
    }

    /**
     * {@inheritdoc}
     */
    public function save(MenuModel $menu)
    {
        $this->menuResource->save($menu);
        return $menu;
    }

    /**
     * Get menu Item by ID
     *
     * @param int $id
     * @return \Ewave\Navigation\Model\Menu
     */
    public function getById($id)
    {
        $menu = $this->menuFactory->create();
        $this->menuResource->load($menu, $id);
        return $menu;
    }

    /**
     * {@inheritdoc}
     */
    public function getListBySetId($setId, $isLoggedIn)
    {
        if (isset($this->menuItemsBySet[$setId])) {
            return $this->menuItemsBySet[$setId];
        }

        /** @var \Ewave\Navigation\Model\ResourceModel\Menu\Collection $collection */
        $collection = $this->menuCollectionFactory->create();
        $collection = $collection->addSetFilter($setId)
            ->addStoreFilter()
            ->joinTypeInfo()
            ->addFieldToFilter(MenuModel::MENU_ITEM_STATUS, ['eq' => MenuModel::MENU_ITEM_STATUS_ENABLED]);
        $collection->addFieldToFilter(
            MenuModel::MENU_ITEM_IS_LOGGED_IN,
            ['in' => [MenuModel::MENU_ITEM_FOR_ALL_USERS, $isLoggedIn]]
        );
        $collection->addOrder(MenuModel::MENU_ITEM_LEVEL, MenuModel::SORT_ORDER_ASC);
        $collection->addOrder(MenuModel::MENU_ITEM_POSITION, MenuModel::SORT_ORDER_ASC);
        $collection->addOrder(MenuModel::MENU_ITEM_PARENT_MENU_ITEM_ID, MenuModel::SORT_ORDER_ASC);
        $collection->addOrder(MenuModel::MENU_ITEM_ID, MenuModel::SORT_ORDER_ASC);
        $this->menuItemsBySet[$setId] = $collection;
        return $collection;
    }

    /**
     * Delete Menu Item
     *
     * @param \Ewave\Navigation\Model\Menu $menu
     * @return Menu
     */
    public function delete(MenuModel $menu)
    {
        return $this->deleteById($menu->getId());
    }

    /**
     * Delete full item by Item ID
     *
     * @param int $id
     * @return Menu
     */
    public function deleteById($id)
    {
        return $this->menuResource->delete($this->getById($id));
    }

    /**
     * Remove menu item info by store ID and entity ID
     *
     * @param int $menuId
     * @param int $storeId
     * @return Menu
     */
    public function deleteByIdAndStoreId($menuId, $storeId)
    {
        return $this->menuResource->deleteByIdAndStoreId($menuId, $storeId);
    }

    /**
     * Update items status
     *
     * @param [] $ids
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status)
    {
        return $this->menuResource->updateStatus($ids, $status);
    }
}
