<?php

namespace Digidirect\Navigation\Model\Admin;

use Digidirect\Navigation\Helper\Data;
use Magento\AdminGws\Model\Role;
use Digidirect\Navigation\Model\Menu;

/**
 * EE version magento. Uses AdminGws extension
 */
class Restrictor implements RestrictorInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Role
     */
    protected $role;

    /**
     * Restrictor constructor.
     * @param Role $role
     * @param Data $data
     */
    public function __construct(Role $role, Data $data)
    {
        $this->role = $role;
        $this->helper = $data;
    }

    /**
     * @param Menu $menu
     * @return bool
     */
    public function isReadOnly(Menu $menu)
    {
        if ($this->role->getIsAll()) {
            return false;
        }

        $menuStoreIds = $menu->getMenuStoreId();
        if (!is_array($menuStoreIds)) {
            $menuStoreIds = [];
        }
        if ($this->role->getIsStoreLevel()) {
            $disallowedStoreIds = $this->role->getDisallowedStoreIds();
            $key = array_search(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $disallowedStoreIds);
            if (false !== $key) {
                unset($disallowedStoreIds[$key]);
            }

            if (!$this->role->getDisallowedWebsiteIds() && !$disallowedStoreIds) {
                return false;
            }

            if (!$this->role->hasStoreAccess($this->helper->getStoreId())) {
                return true;
            }

            if (in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $menuStoreIds)) {
                if ($this->role->getIsStoreLevel()) {
                    return true;
                }
            }

            if (in_array($this->helper->getCurrentStoreId(), $menuStoreIds)) {
                return false;
            }
        } else {
            $disallowedStores = $this->role->getDisallowedStores();
            $disallowedStoreIds = $this->role->getDisallowedStoreIds();
            $disallowedWebsiteIds = [];
            /**
             * @var $store \Magento\Store\Model\Store
             */
            foreach ($disallowedStores as $store) {
                if ($store->getId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                    continue;
                }

                $disallowedWebsiteIds[] = $store->getWebsiteId();
            }
            if (!in_array(0, $menuStoreIds)) {
                return false;
            } else {
                if (!in_array($this->helper->getCurrentStoreId(), $disallowedStoreIds)) {
                    return false;
                }
                return true;
            }
        }
        return true;
    }
}
