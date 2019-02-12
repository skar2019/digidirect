<?php
namespace Ewave\AbstractAttributes\Api\Data;

/**
 * Interface for AbstractAttribute search results.
 * @api
 */
interface AbstractAttributeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get abstract attributes list.
     * @return \Ewave\AbstractAttributes\Api\Data\AbstractAttributeInterface[]
     */
    public function getItems();

    /**
     * Set abstract attributes list.
     * @param \Ewave\AbstractAttributes\Api\Data\AbstractAttributeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
