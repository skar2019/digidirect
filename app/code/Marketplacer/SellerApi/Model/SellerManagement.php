<?php

namespace Marketplacer\SellerApi\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Base\Api\CacheInvalidatorInterface;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterfaceFactory;
use Marketplacer\SellerApi\Api\SellerManagementInterface;
use Marketplacer\SellerApi\Api\SellerRepositoryInterface;

class SellerManagement implements SellerManagementInterface
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @var SellerRepositoryInterface
     */
    protected $sellerRepository;

    /**
     * @var MarketplacerSellerInterfaceFactory
     */
    protected $sellerFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CacheInvalidatorInterface
     */
    protected $cacheInvalidator;

    /**
     * SellerManagement constructor.
     * @param SellerRepositoryInterface $sellerRepository
     * @param MarketplacerSellerInterfaceFactory $sellerFactory
     * @param StoreManagerInterface $storeManager
     * @param CacheInvalidatorInterface $cacheInvalidator
     */
    public function __construct(
        SellerRepositoryInterface $sellerRepository,
        MarketplacerSellerInterfaceFactory $sellerFactory,
        StoreManagerInterface $storeManager,
        CacheInvalidatorInterface $cacheInvalidator
    ) {
        $this->sellerRepository = $sellerRepository;
        $this->sellerFactory = $sellerFactory;
        $this->storeManager = $storeManager;
        $this->cacheInvalidator = $cacheInvalidator;
    }

    /**
     * @inheritDoc
     */
    public function getById($sellerId, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->sellerRepository->getById($sellerId, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        null !== $searchCriteria->getPageSize() ?: $searchCriteria->setPageSize(static::DEFAULT_PAGE_SIZE);

        return $this->sellerRepository->getList($searchCriteria);
    }

    /**
     * @inheritDoc
     */
    public function save(MarketplacerSellerInterface $seller, $sellerId = null, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $sellerData = $seller->getData();
        if ($sellerId) {
            $sellerModel = $this->sellerRepository->getById($sellerId, $storeId);
        } else {
            $sellerModel = $this->sellerFactory->create();
        }

        $sellerModel->addData($sellerData);

        $this->sellerRepository->save($sellerModel);

        if ($this->cacheInvalidator) {
            $this->cacheInvalidator->invalidate();
        }

        return $sellerModel;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($sellerId)
    {
        $result = $this->sellerRepository->deleteById($sellerId);

        if ($this->cacheInvalidator) {
            $this->cacheInvalidator->invalidate();
        }

        return $result;
    }
}
