<?php
namespace Ewave\AI\Api\Data;

/**
 * Interface ScheduleSearchResultInterface
 *
 * @package Ewave\AI\Api\Data
 * @api
 */
interface ScheduleSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Ewave\AI\Api\Data\ScheduleInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Ewave\AI\Api\Data\ScheduleInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
