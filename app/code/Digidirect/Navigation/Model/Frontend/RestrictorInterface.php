<?php

namespace Digidirect\Navigation\Model\Frontend;

use Digidirect\Navigation\Model\Menu;

/**
 * Interface is introduced if we need to restrict particular item's output on frontend
 */
interface RestrictorInterface
{
    public function isAvailable(Menu $menu): bool;
}
