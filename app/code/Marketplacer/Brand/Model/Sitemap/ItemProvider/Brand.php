<?php

namespace Marketplacer\Brand\Model\Sitemap\ItemProvider;

use Magento\Sitemap\Model\ItemProvider\CategoryConfigReader;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Url as UrlHelper;
use Marketplacer\Brand\Helper\Config as ConfigHelper;

class Brand implements ItemProviderInterface
{
    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var SitemapItemInterfaceFactory
     */
    protected $sitemapItemFactory;

    /**
     * Config reader
     *
     * @var CategoryConfigReader
     */
    protected $categoryConfigReader;

    /**
     * @param BrandRepositoryInterface $brandRepository
     * @param UrlHelper $urlHelper
     * @param ConfigHelper $configHelper
     * @param SitemapItemInterfaceFactory $sitemapItemFactory
     * @param CategoryConfigReader $categoryConfigReader
     */
    public function __construct(
        BrandRepositoryInterface $brandRepository,
        UrlHelper $urlHelper,
        ConfigHelper $configHelper,
        SitemapItemInterfaceFactory $sitemapItemFactory,
        CategoryConfigReader $categoryConfigReader
    ) {
        $this->brandRepository = $brandRepository;
        $this->urlHelper = $urlHelper;
        $this->configHelper = $configHelper;
        $this->sitemapItemFactory = $sitemapItemFactory;
        $this->categoryConfigReader = $categoryConfigReader;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems($storeId)
    {
        if (!$this->configHelper->isEnabledOnStorefront($storeId)) {
            return [];
        }

        $brandListingItem = $this->sitemapItemFactory->create([
            'url'             => $this->urlHelper->getBrandListingUrl($storeId, ['_request_path_only' => 1]),
            'updatedAt'       => null,
            'images'          => null,
            'priority'        => $this->categoryConfigReader->getPriority($storeId),
            'changeFrequency' => $this->categoryConfigReader->getChangeFrequency($storeId),
        ]);

        $brands = $this->brandRepository->getAllDisplayedBrands($storeId);

        $brandItems = array_map(function (BrandInterface $brand) use ($storeId) {
            return $this->sitemapItemFactory->create([
                'url'             => $this->urlHelper->getBrandUrl($brand, ['_request_path_only' => 1]),
                'updatedAt'       => $brand->getUpdatedAt(),
                'images'          => null,
                'priority'        => $this->categoryConfigReader->getPriority($storeId),
                'changeFrequency' => $this->categoryConfigReader->getChangeFrequency($storeId),
            ]);
        }, $brands);

        $sitemapItems = array_merge([$brandListingItem], $brandItems);

        return $sitemapItems;
    }
}
