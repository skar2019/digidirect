<?php

namespace Marketplacer\SellerApi\Api\Data;

interface MarketplacerSellerSearchResultsInterface
{
    /**
     * Get seller list.
     *
     * @return \Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface[]
     */
    public function getSellers();

    /**
     * Set seller list.
     *
     * @param \Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface[] $sellers
     * @return $this
     */
    public function setSellers(array $sellers = null);

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
