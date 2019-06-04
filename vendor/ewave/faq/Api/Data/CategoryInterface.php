<?php

namespace Ewave\Faq\Api\Data;

interface CategoryInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const STORE_ID = 'store_id';
    const IDENTIFIER = 'identifier';

    /**
     * @return []
     */
    public function getStores();

    /**
     * @return string
     */
    public function getIdentifier(): string;
}
