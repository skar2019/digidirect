<?php

namespace Digidirect\Navigation\Model\Admin;

use Digidirect\Navigation\Model\Menu;

/**
 * Introduced to restrict menu item management depending on conditions
 */
interface RestrictorInterface
{
    /**
     * @param Menu $menu
     * @return bool
     */
    public function isReadOnly(Menu $menu);
}
