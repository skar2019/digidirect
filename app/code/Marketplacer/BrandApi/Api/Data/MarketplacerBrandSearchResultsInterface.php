<?php

namespace Marketplacer\BrandApi\Api\Data;

interface MarketplacerBrandSearchResultsInterface
{
    /**
     * Get brand list.
     *
     * @return \Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface[]
     */
    public function getBrands();

    /**
     * Set brand list.
     *
     * @param \Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface[] $brands
     * @return $this
     */
    public function setBrands(array $brands = null);

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface
     */
    public function getSearchCriteria();

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount();

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount);
}
