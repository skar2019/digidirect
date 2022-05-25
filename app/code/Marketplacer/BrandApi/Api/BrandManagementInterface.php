<?php

namespace Marketplacer\BrandApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandSearchResultsInterface;

interface BrandManagementInterface
{
    /**
     * @param int | string | null $brandId
     * @param int | string | null $storeId
     * @return MarketplacerBrandInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getById($brandId, $storeId = null);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return MarketplacerBrandSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param MarketplacerBrandInterface $brand
     * @param int | string | null $brandId
     * @param int|string|null $storeId
     * @return MarketplacerBrandInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws AlreadyExistsException
     */
    public function save(MarketplacerBrandInterface $brand, $brandId = null, $storeId = null);

    /**
     * @param int | string | null $brandId
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteById($brandId);
}
