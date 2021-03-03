<?php
namespace Digidirect\Navigation\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for navigation set search results.
 */
interface SetSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get sets list.
     *
     * @return \Digidirect\Navigation\Api\Data\SetInterface[]
     */
    public function getItems();

    /**
     * Set sets list.
     *
     * @param \Digidirect\Navigation\Api\Data\SetInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
