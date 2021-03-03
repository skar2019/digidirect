<?php

namespace Digidirect\Navigation\Model\Admin;

/**
 * @since 1.3.0
 *
 * Class is introduced for better management between CE and EE versions
 */
class StoreDropdownAcl
{
    /**
     * @var StoreDropdownRestrictorInterface|null
     */
    protected $restirctor;

    /**
     * StoreDropdownAcl constructor.
     * @param StoreDropdownRestrictorInterface|null $restrictor
     */
    public function __construct(StoreDropdownRestrictorInterface $restrictor = null)
    {
        $this->restirctor = $restrictor;
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function showStore($storeId): bool
    {
        if (null === $this->restirctor) {
            return true;
        }

        return $this->restirctor->showStore($storeId);
    }

    /**
     * @return bool
     */
    public function showAllStoreViews(): bool
    {
        if (null === $this->restirctor) {
            return true;
        }

        return $this->restirctor->showAllStoreViews();
    }
}
