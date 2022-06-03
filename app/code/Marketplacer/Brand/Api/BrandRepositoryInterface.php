<?php

namespace Marketplacer\Brand\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandSearchResultsInterface;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;

interface BrandRepositoryInterface extends \Marketplacer\BrandApi\Api\BrandRepositoryInterface
{
    /**
     * @param int | string $brandId
     * @param int|string|null $storeId
     * @return BrandInterface | MarketplacerBrandInterface
     * @throws NoSuchEntityException
     */
    public function getById($brandId, $storeId = null);

    /**
     * @param array $brandIds
     * @param int | string | null $storeId
     * @return BrandInterface[] | MarketplacerBrandInterface[]
     */
    public function getByIds(array $brandIds = [], $storeId = null);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return MarketplacerBrandSearchResultsInterface|mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @return BrandInterface[] | MarketplacerBrandInterface[]
     * @throws LocalizedException
     */
    public function getAllBrandIds();

    /**
     * @param int|string|null $storeId
     * @return BrandInterface[] | MarketplacerBrandInterface[]
     * @throws LocalizedException
     */
    public function getAllDisplayedBrands($storeId = null);

    /**
     * @param BrandInterface | MarketplacerBrandInterface $brand
     * @return BrandInterface | MarketplacerBrandInterface
     * @throws LocalizedException
     */
    public function save($brand);

    /**
     * @param int $brandId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($brandId);
}
