<?php

namespace Marketplacer\BrandApi\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Base\Api\CacheInvalidatorInterface;
use Marketplacer\BrandApi\Api\BrandManagementInterface;
use Marketplacer\BrandApi\Api\BrandRepositoryInterface;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterfaceFactory;

class BrandManagement implements BrandManagementInterface
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var MarketplacerBrandInterfaceFactory
     */
    protected $brandFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CacheInvalidatorInterface
     */
    protected $cacheInvalidator;

    /**
     * BrandManagement constructor.
     * @param BrandRepositoryInterface $brandRepository
     * @param MarketplacerBrandInterfaceFactory $brandFactory
     * @param StoreManagerInterface $storeManager
     * @param CacheInvalidatorInterface $cacheInvalidator
     */
    public function __construct(
        BrandRepositoryInterface $brandRepository,
        MarketplacerBrandInterfaceFactory $brandFactory,
        StoreManagerInterface $storeManager,
        CacheInvalidatorInterface $cacheInvalidator
    ) {
        $this->brandRepository = $brandRepository;
        $this->brandFactory = $brandFactory;
        $this->storeManager = $storeManager;
        $this->cacheInvalidator = $cacheInvalidator;
    }

    /**
     * @inheritDoc
     */
    public function getById($brandId, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->brandRepository->getById($brandId, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        null !== $searchCriteria->getPageSize() ?: $searchCriteria->setPageSize(static::DEFAULT_PAGE_SIZE);

        return $this->brandRepository->getList($searchCriteria);
    }

    /**
     * @inheritDoc
     */
    public function save(MarketplacerBrandInterface $brand, $brandId = null, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $brandData = $brand->getData();
        if ($brandId) {
            $brandModel = $this->brandRepository->getById($brandId, $storeId);
        } else {
            $brandModel = $this->brandFactory->create();
        }

        $brandModel->addData($brandData);

        $this->brandRepository->save($brandModel);

        if ($this->cacheInvalidator) {
            $this->cacheInvalidator->invalidate();
        }

        return $brandModel;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($brandId)
    {
        $result = $this->brandRepository->deleteById($brandId);

        if ($this->cacheInvalidator) {
            $this->cacheInvalidator->invalidate();
        }

        return $result;
    }
}
