<?php

namespace Ewave\ProductOverlay\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for product overlay search results.
 * @api
 */
interface OverlaySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get overlays list.
     *
     * @return \Ewave\ProductOverlay\Api\Data\OverlayInterface[]
     */
    public function getItems();

    /**
     * Set overlays list.
     *
     * @param \Ewave\ProductOverlay\Api\Data\OverlayInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
