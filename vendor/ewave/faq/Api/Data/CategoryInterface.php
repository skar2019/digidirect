<?php
namespace Ewave\Faq\Api\Data;

/**
 * Interface CategoryInterface
 * @package Ewave\Faq\Api\Data
 */
interface CategoryInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const STORE_ID = 'store_id';

    /**
     * @return []
     */
    public function getStores();
}
