<?php

namespace Digidirect\Navigation\Model\Admin;

use Digidirect\Navigation\Model\Menu;

/**
 * Acl class.
 * Used as decorator for restrictor
 */
class Acl
{
    /**
     * @var RestrictorInterface|null
     */
    protected $restrictor;

    /**
     * Acl constructor.
     * @param RestrictorInterface|null $restrictor
     */
    public function __construct(RestrictorInterface $restrictor = null)
    {
        $this->restrictor = $restrictor;
    }

    /**
     * @param Menu $menu
     * @return bool
     */
    public function isReadOnly(Menu $menu)
    {
        if (null === $this->restrictor) {
            return false;
        }

        return $this->restrictor->isReadOnly($menu);
    }
}
