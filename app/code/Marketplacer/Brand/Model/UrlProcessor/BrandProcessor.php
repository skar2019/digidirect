<?php

namespace Marketplacer\Brand\Model\UrlProcessor;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlFactory as MagentoUrlFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\UrlRewrite\Model\StorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Config as ConfigHelper;

/**
 * Class BrandProcessor
 * @package Marketplacer\Brand\Model\UrlProcessor
 */
class BrandProcessor
{
    const URL_ENTITY_TYPE = 'marketplacer-brand';
    const BRAND_LIST_ROUTE_PATH = 'marketplacer/brand/index';
    const BRAND_VIEW_ROUTE_PATH = 'marketplacer/brand/view';
    const BRAND_LIST_TARGET_PATH_PATTERN = self::BRAND_LIST_ROUTE_PATH;
    const BRAND_VIEW_TARGET_PATH_PATTERN = self::BRAND_VIEW_ROUTE_PATH . '/brand_id/%s';
    const REQUEST_PATH_PATTERN = '%s/%s';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * ProcessorAbstract constructor.
     * @param StoreManagerInterface $storeManager
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param StorageInterface $storage
     * @param ConfigHelper $configHelper
     * @param BrandRepositoryInterface $brandRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        UrlRewriteFactory $urlRewriteFactory,
        StorageInterface $storage,
        ConfigHelper $configHelper,
        BrandRepositoryInterface $brandRepository
    ) {
        $this->storeManager = $storeManager;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->storage = $storage;
        $this->configHelper = $configHelper;
        $this->brandRepository = $brandRepository;
    }

    /**
     * Generate url rewrites
     * @param BrandInterface $brand
     * @param array $storeIds
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws UrlAlreadyExistsException
     */
    public function processBrandUrlRewrites(BrandInterface $brand, array $storeIds = [])
    {
        $urls = [];
        $brandId = $brand->getBrandId();
        if (!$brandId) {
            return false;
        }

        $stores = $this->getProcessedStores($storeIds);

        $targetPath = sprintf(self::BRAND_VIEW_TARGET_PATH_PATTERN, $brandId);

        $brandStoreRecords = $this->brandRepository->getAllStoreRecordsById($brandId);

        foreach ($stores as $store) {
            $brand = $brandStoreRecords[$store->getId()] ?? $brandStoreRecords[Store::DEFAULT_STORE_ID];
            $brandUrlKey = $brand->getUrlKey();
            if (!$brandUrlKey) {
                continue;
            }

            $baseUrlKey = $this->configHelper->getBaseUrlKey($store->getId());
            if (!$baseUrlKey) {
                continue;
            }
            $urlSuffix = $this->configHelper->getUrlSuffix($store->getId()) ?? '';
            $requestPath = sprintf(self::REQUEST_PATH_PATTERN, $baseUrlKey, $brandUrlKey) . $urlSuffix;

            $urls[] = $this->urlRewriteFactory->create()
                ->setEntityType(self::URL_ENTITY_TYPE)
                ->setEntityId($brandId)
                ->setRequestPath($requestPath)
                ->setTargetPath($targetPath)
                ->setStoreId($store->getId());
        }

        $this->storage->replace($urls);
        return true;
    }

    /**
     * Delete url rewrites
     * @param BrandInterface $brand
     * @param string | int | null $storeId
     * @return bool
     */
    public function deleteUrlRewrites(BrandInterface $brand, $storeId = null)
    {
        $brandId = $brand->getBrandId();
        if (!$brandId) {
            return false;
        }

        $filterData = [
            UrlRewrite::ENTITY_ID   => $brandId,
            UrlRewrite::ENTITY_TYPE => BrandProcessor::URL_ENTITY_TYPE,
        ];

        if ($storeId) {
            $filterData[UrlRewrite::STORE_ID] = $storeId;
        }

        $this->storage->deleteByData($filterData);

        return true;
    }

    /**
     * Delete url rewrites for
     * @param string | int | null $storeId
     * @return bool
     */
    public function deleteListingUrlRewrites($storeId = null)
    {
        $filterData = [
            UrlRewrite::ENTITY_ID   => 0,
            UrlRewrite::ENTITY_TYPE => BrandProcessor::URL_ENTITY_TYPE,
        ];

        if ($storeId) {
            $filterData[UrlRewrite::STORE_ID] = $storeId;
        }

        $this->storage->deleteByData($filterData);

        return true;
    }

    /**
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws UrlAlreadyExistsException
     */
    public function processBrandListingUrlRewrites(array $storeIds = [])
    {
        $urls = [];

        $stores = $this->getProcessedStores($storeIds);

        foreach ($stores as $store) {
            $baseUrlKey = $this->configHelper->getBaseUrlKey($store->getId());
            if (!$baseUrlKey) {
                continue;
            }
            $urlSuffix = $this->configHelper->getUrlSuffix($store->getId()) ?? '';
            $requestPath = $baseUrlKey . $urlSuffix;

            $urls[] = $this->urlRewriteFactory->create()
                ->setEntityType(self::URL_ENTITY_TYPE)
                ->setEntityId(0)
                ->setRequestPath($requestPath)
                ->setTargetPath(self::BRAND_LIST_TARGET_PATH_PATTERN)
                ->setStoreId($store->getId());
        }

        $this->storage->replace($urls);
        return true;
    }

    /**
     * @param array $storeIds
     * @return StoreInterface[]
     */
    protected function getProcessedStores($storeIds = [])
    {
        $stores = $this->storeManager->getStores();

        if ($storeIds) {
            $storeIds = (array)$storeIds;
            $stores = array_filter(
                $stores,
                function ($store) use ($storeIds) {
                    return in_array($store->getId(), $storeIds);
                }
            );
        }

        return $stores;
    }
}
