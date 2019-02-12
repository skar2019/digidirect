<?php

namespace Ewave\Navigation\Model\Admin;

use Ewave\Navigation\Model\Menu;

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
