<?php

namespace Digidirect\Navigation\Model\Admin;

/**
 * Interface is introduced to manage store views dropdown
 * @since 1.3.0
 */
interface StoreDropdownRestrictorInterface
{
    /**
     * @param null $storeId
     * @return bool
     */
    public function showStore($storeId = null): bool;

    /**
     * @return bool
     */
    public function showAllStoreViews(): bool;
}
