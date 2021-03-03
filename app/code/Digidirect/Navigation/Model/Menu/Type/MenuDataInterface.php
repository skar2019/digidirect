<?php

namespace Digidirect\Navigation\Model\Menu\Type;

use Digidirect\Navigation\Api\Data\MenuItemInterface;

/**
 * Interface UrlInterface
 * @package Digidirect\Navigation\Model\Menu
 */
interface MenuDataInterface
{
    /**
     * Get url depend on type
     *
     * @return string
     */
    public function getUrl();

    /**
     * Get menu data array depends on type
     *
     * @return []
     */
    public function getMenuData();

    /**
     * Check menu item availability depends on type
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * @param MenuItemInterface $menuItem
     * @return MenuDataInterface
     */
    public function setItem(MenuItemInterface $menuItem): self;
}
