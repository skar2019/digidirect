<?php

namespace Marketplacer\SellerApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerSearchResultsInterface;

interface SellerManagementInterface
{
    /**
     * @param int | string | null $sellerId
     * @param int | null $storeId
     * @return MarketplacerSellerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getById($sellerId, $storeId = null);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return MarketplacerSellerSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param MarketplacerSellerInterface $seller
     * @param int | string | null $sellerId
     * @param int|string|null $storeId
     * @return MarketplacerSellerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws AlreadyExistsException
     */
    public function save(MarketplacerSellerInterface $seller, $sellerId = null, $storeId = null);

    /**
     * @param int | string | null $sellerId
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteById($sellerId);
}
