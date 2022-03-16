<?php

namespace Marketplacer\BrandApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandSearchResultsInterface;

interface BrandRepositoryInterface
{
    /**
     * @param int | string $brandId
     * @param int|string|null $storeId
     * @return MarketplacerBrandInterface
     * @throws NoSuchEntityException
     */
    public function getById($brandId, $storeId = null);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return MarketplacerBrandSearchResultsInterface|mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param MarketplacerBrandInterface $brand
     * @return MarketplacerBrandInterface
     * @throws LocalizedException
     */
    public function save(MarketplacerBrandInterface $brand);

    /**
     * @param int | string $brandId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($brandId);
}
