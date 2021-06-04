<?php
namespace Ewave\MyStoreWidget\Api\Data;

interface MyStoreSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get AbstractEntity list.
     * @return \Ewave\AbstractEntity\Api\Data\AbstractEntityInterface[]
     */
    public function getItems();

    /**
     * Set identifier list.
     * @param \Ewave\AbstractEntity\Api\Data\AbstractEntityInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
