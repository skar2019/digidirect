<?php

namespace Marketplacer\SellerApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerSearchResultsInterface;

interface SellerRepositoryInterface
{
    /**
     * @param int | string $sellerId
     * @param int|string|null $storeId
     * @return MarketplacerSellerInterface
     * @throws NoSuchEntityException
     */
    public function getById($sellerId, $storeId = null);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return MarketplacerSellerSearchResultsInterface|mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param MarketplacerSellerInterface $seller
     * @return MarketplacerSellerInterface
     * @throws LocalizedException
     */
    public function save($seller);

    /**
     * @param int | string $sellerId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($sellerId);
}
