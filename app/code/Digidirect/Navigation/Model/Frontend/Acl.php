<?php

namespace Digidirect\Navigation\Model\Frontend;

use Digidirect\Navigation\Model\Menu;

/**
 * Frontend Acl class.
 * Used as decorator for restrictor
 *
 * @since 1.3.0
 */
class Acl
{
    /**
     * @var RestrictorInterface|null
     */
    protected $restrictor;

    /**
     * Acl constructor.
     *
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
    public function isAvailable(Menu $menu): bool
    {
        if (null === $this->restrictor) {
            return true;
        }

        return $this->restrictor->isAvailable($menu);
    }
}
