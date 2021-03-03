<?php
namespace Digidirect\Navigation\Api;

use Digidirect\Navigation\Model\ResourceModel\Menu;

/**
 * Navigation menu CRUD interface.
 */
interface MenuRepositoryInterface extends AbstractNavigationInterface
{
    /**
     * Save menu.
     *
     * @param \Digidirect\Navigation\Model\Menu $menu
     * @return \Digidirect\Navigation\Model\Menu
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Digidirect\Navigation\Model\Menu $menu);

    /**
     * Retrieve menu.
     *
     * @param int $menuId
     * @return \Digidirect\Navigation\Model\Menu
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($menuId);

    /**
     * Retrieve menus matching the specified criteria.
     *
     * @param int $setId
     * @param bool $isLoggedIn
     * @return \Digidirect\Navigation\Model\ResourceModel\Menu\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListBySetId($setId, $isLoggedIn);

    /**
     * Delete menu.
     *
     * @param \Digidirect\Navigation\Model\Menu $menu
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Digidirect\Navigation\Model\Menu $menu);

    /**
     * Delete navigation menu by ID.
     *
     * @param int $menuId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($menuId);

    /**
     * Delete navigation menu item info by menu ID and Store ID
     *
     * @param int $menuId
     * @param int $storeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return Menu
     */
    public function deleteByIdAndStoreId($menuId, $storeId);

    /**
     * Update items status
     *
     * @param [] $ids
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status);
}
