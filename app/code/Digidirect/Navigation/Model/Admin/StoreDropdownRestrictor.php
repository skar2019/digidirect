<?php

namespace Digidirect\Navigation\Model\Admin;

use Digidirect\Navigation\Model\Registry\Constants;
use Magento\AdminGws\Model\Role as AdminRole;
use Magento\Framework\Registry;
use Digidirect\Navigation\Helper\Data;

/**
 * Dropdown restrictor implementation
 * @since 1.3.0
 */
class StoreDropdownRestrictor implements StoreDropdownRestrictorInterface
{
    /**
     * @var AdminRole
     */
    protected $role;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Role constructor.
     *
     * @param AdminRole $role
     * @param Registry $registry
     * @param Data $dataHelper
     */
    public function __construct(AdminRole $role, Registry $registry, Data $dataHelper)
    {
        $this->role = $role;
        $this->registry = $registry;
        $this->helper = $dataHelper;
    }

    /**
     * @param null $currentStoreId
     * @return bool
     */
    public function showStore($currentStoreId = null): bool
    {
        if ($this->role->getIsAll()) {
            return true;
        }
        if (!$currentStoreId) {
            $currentStoreId = $this->helper->getCurrentStoreId();
        }
        if ($this->role->getIsStoreLevel()) {
            if ($this->isMenuItemAssignedToAllStoreViews()) {
                return false;
            }

            if (!in_array($currentStoreId, $this->getMenuStoreIds())) {
                return false;
            }
        }

        if ($this->role->getIsWebsiteLevel()) {
            if ($this->isMenuItemAssignedToAllStoreViews()) {
                return false;
            }
            return true;
        }

        return true;
    }


    /**
     * If website restriction and menu item has id and assigned to all store views - show
     * If store restriction and menu item has id and assigned to all store views - show
     *
     *
     * @return bool
     */
    public function showAllStoreViews(): bool
    {
        if ($this->role->getIsAll()) {
            return true;
        }
        $menuStoreIds = $this->getMenuStoreIds();

        if ($this->role->getIsStoreLevel()) {
            if (in_array(0, $menuStoreIds)) {
                return true;
            } else {
                return false;
            }
        }

        if ($this->role->getIsWebsiteLevel()) {
            if (in_array(0, $menuStoreIds)) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * @return \Digidirect\Navigation\Model\Menu
     */
    protected function getCurrentMenuItem()
    {
        return $this->registry->registry(Constants::CURRENT_MENU_ITEM);
    }

    /**
     * @return array
     */
    protected function getMenuStoreIds()
    {
        $menuStores = $this->getCurrentMenuItem()->getMenuStoreId();
        if (!is_array($menuStores)) {
            $menuStores = [];
        }

        return $menuStores;
    }

    /**
     * @return bool
     */
    protected function isMenuItemAssignedToAllStoreViews()
    {
        $menuStores = $this->getMenuStoreIds();
        if (!is_array($menuStores)) {
            $menuStores = [];
        }

        return in_array(0, $menuStores);
    }
}
