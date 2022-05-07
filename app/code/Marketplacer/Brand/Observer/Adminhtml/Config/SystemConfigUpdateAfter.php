<?php

namespace Marketplacer\Brand\Observer\Adminhtml\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Helper\Config;
use Marketplacer\Brand\Helper\Data;
use Marketplacer\Brand\Model\ResourceModel\Brand as BrandResource;
use Marketplacer\Brand\Model\UrlProcessor\BrandProcessor as BrandUrlProcessor;

/**
 * Class SystemConfigUpdateAfter
 * @package Marketplacer\Brand\Observer\Adminhtml\Config
 */
class SystemConfigUpdateAfter implements ObserverInterface
{
    protected const TRIGGERING_XML_PATHS = [
        Config::XML_PATH_SEO_BASE_URL_KEY,
        Config::XML_PATH_SEO_URL_SUFFIX,
    ];

    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var BrandUrlProcessor
     */
    protected $brandUrlProcessor;

    /**
     * @var BrandResource
     */
    protected $brandResource;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @param BrandRepositoryInterface $brandRepository
     * @param BrandResource $brandResource
     * @param BrandUrlProcessor $brandUrlProcessor
     * @param Config $brandConfigHelper
     */
    public function __construct(
        BrandRepositoryInterface $brandRepository,
        BrandResource $brandResource,
        BrandUrlProcessor $brandUrlProcessor,
        Config $brandConfigHelper
    ) {
        $this->brandRepository = $brandRepository;
        $this->brandResource = $brandResource;
        $this->brandUrlProcessor = $brandUrlProcessor;
        $this->configHelper = $brandConfigHelper;
    }

    /**
     * Execute
     * @param EventObserver $observer
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws UrlAlreadyExistsException
     */
    public function execute(EventObserver $observer)
    {
        $triggers = array_intersect($observer->getChangedPaths(), static::TRIGGERING_XML_PATHS);

        if (!$triggers) {
            return $this;
        }

        $this->updateUrlRewrites();

        return $this;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws UrlAlreadyExistsException
     */
    protected function updateUrlRewrites()
    {
        $this->brandUrlProcessor->processBrandListingUrlRewrites();

        $allIds = $this->brandResource->getAllBrandIds();

        $idsChunks = array_chunk($allIds, Data::BULK_OPERATIONS_CHUNK_SIZE);

        foreach ($idsChunks as $ids) {
            $brands = $this->brandRepository->getByIds($ids, Store::DEFAULT_STORE_ID);
            foreach ($brands as $brand) {
                $brand->processUrlRewrites();
            }
        }
    }
}
