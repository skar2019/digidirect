<?php
namespace Ewave\Navigation\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for navigation set search results.
 */
interface SetSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get sets list.
     *
     * @return \Ewave\Navigation\Api\Data\SetInterface[]
     */
    public function getItems();

    /**
     * Set sets list.
     *
     * @param \Ewave\Navigation\Api\Data\SetInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
