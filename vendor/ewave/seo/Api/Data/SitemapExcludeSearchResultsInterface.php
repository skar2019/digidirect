<?php
namespace Ewave\SEO\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for sitemap exclude search results.
 * @api
 */
interface SitemapExcludeSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list.
     *
     * @return \Ewave\SEO\Api\Data\SitemapExcludeInterface[]
     */
    public function getItems();

    /**
     * Set list.
     *
     * @param \Ewave\SEO\Api\Data\SitemapExcludeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
