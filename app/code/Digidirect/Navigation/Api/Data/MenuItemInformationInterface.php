<?php

namespace Digidirect\Navigation\Api\Data;

/**
 * Interface MenuItemInformationInterface
 * Created for more obvious usage of menu item object
 */
interface MenuItemInformationInterface
{
    const TITLE = 'title';
    const IDENTIFIER = 'identifier';
    const CUSTOM_OPTIONS = 'custom_options';
    const POSITION = 'position';
    const CONTENT = 'content';
    const PARENT_MENU_ITEM_ID = 'parent_menu_item_id';
    const LEVEL = 'level';
    const PATH = 'path';
    const STATUS = 'status';
    const STORE_ID = 'store_id';
    const IS_LOGGED_IN = 'is_logged_in';
    const ID = 'id';

    /**
     * @return mixed
     */
    public function getTitle();

    /**
     * @param string $title
     * @return mixed
     */
    public function setTitle($title);

    /**
     * @return mixed
     */
    public function getIdentifier();

    /**
     * @return mixed
     */
    public function getPosition();

    /**
     * @param int $position
     * @return mixed
     */
    public function setPosition($position);

    /**
     * @return mixed
     */
    public function getContent();

    /**
     * @param string $content
     * @return mixed
     */
    public function setContent($content);

    /**
     * @return mixed
     */
    public function getParentMenuItemId();

    /**
     * @param int $parentMenuItemId
     * @return mixed
     */
    public function setParentMenuItemId($parentMenuItemId);

    /**
     * @return mixed
     */
    public function getCustomOptions();

    /**
     * @param string|[] $customOptions
     * @return mixed
     */
    public function setCustomOptions($customOptions);

    /**
     * @return mixed
     */
    public function getLevel();

    /**
     * @param int $level
     * @return mixed
     */
    public function setLevel($level);

    /**
     * @return mixed
     */
    public function getPath();

    /**
     * @param string $path
     * @return mixed
     */
    public function setPath($path);

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @param int $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * @return mixed
     */
    public function getStoreId();

    /**
     * @param int $storeId
     * @return mixed
     */
    public function setStoreId($storeId);

    /**
     * @return mixed
     */
    public function getIsLoggedIn();

    /**
     * @param int|bool $isLoggedIn
     * @return mixed
     */
    public function setIsLoggedIn($isLoggedIn);

    /**
     * @return mixed
     */
    public function getMenuId();

    /**
     * @param string|int $menuId
     * @return mixed
     */
    public function setMenuId($menuId);

    /**
     * @param string $option
     * @return mixed
     */
    public function getCustomOption($option);
}
