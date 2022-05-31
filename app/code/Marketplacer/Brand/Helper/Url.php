<?php

namespace Marketplacer\Brand\Helper;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlFactory as MagentoUrlFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Model\UrlProcessor\BrandProcessor;

/**
 * Class Url
 * @package Marketplacer\Brand\Helper
 */
class Url extends AbstractHelper
{
    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var MagentoUrlFactory
     */
    protected $urlFactory;

    /**
     * Url constructor.
     * @param Context $context
     * @param BrandRepositoryInterface $brandRepositoryInterface
     * @param StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     * @param MagentoUrlFactory $urlFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        BrandRepositoryInterface $brandRepositoryInterface,
        StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder,
        MagentoUrlFactory $urlFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->brandRepository = $brandRepositoryInterface;
        $this->storeManager = $storeManager;
        $this->urlFinder = $urlFinder;
        $this->urlFactory = $urlFactory;
        parent::__construct($context);
    }

    /**
     * Get brand url
     * @param BrandInterface $brand
     * @param array $params
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBrandUrl(BrandInterface $brand, $params = [])
    {
        $routePath = '';
        $requestPath = '';
        $routeParams = $params;

        $brandId = $brand->getBrandId();
        $storeId = $brand->getStoreId();
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $filterData = [
            UrlRewrite::ENTITY_ID   => $brandId,
            UrlRewrite::ENTITY_TYPE => BrandProcessor::URL_ENTITY_TYPE,
            UrlRewrite::STORE_ID    => $storeId,
        ];

        $rewrite = $this->urlFinder->findOneByData($filterData);
        if ($rewrite) {
            $requestPath = $rewrite->getRequestPath();
        }

        if (isset($routeParams['_scope'])) {
            $storeId = $this->storeManager->getStore($routeParams['_scope'])->getId();
        }

        if ($storeId != $this->storeManager->getStore()->getId()) {
            $routeParams['_scope_to_url'] = true;
        }

        if (empty($requestPath)) {
            $requestPath = sprintf(BrandProcessor::BRAND_VIEW_TARGET_PATH_PATTERN, $brandId);
        }

        if (isset($routeParams['_request_path_only'])) {
            return $requestPath;
        }

        $routeParams['_direct'] = $requestPath;

        // Reset cached URL instance GET query params
        if (!isset($routeParams['_query'])) {
            $routeParams['_query'] = [];
        }

        return $this->getUrlInstance()->setScope($storeId)->getUrl($routePath, $routeParams);
    }

    /**
     * @param int $brandId
     * @param array $params
     * @return bool|string
     * @throws NoSuchEntityException
     */
    public function getBrandUrlById($brandId, $storeId = null, $params = [])
    {
        $brand = $this->brandRepository->getById($brandId, $storeId);
        if ($brand->getBrandId()) {
            return $this->getBrandUrl($brand, $params);
        }

        return false;
    }

    /**
     * Get brand listing url
     * @param array $params
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBrandListingUrl($storeId = null, $params = [])
    {
        $routePath = '';
        $requestPath = '';
        $routeParams = $params;

        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $filterData = [
            UrlRewrite::ENTITY_ID   => 0,
            UrlRewrite::ENTITY_TYPE => BrandProcessor::URL_ENTITY_TYPE,
            UrlRewrite::STORE_ID    => $storeId,
        ];

        $rewrite = $this->urlFinder->findOneByData($filterData);
        if ($rewrite) {
            $requestPath = $rewrite->getRequestPath();
        }

        if (isset($routeParams['_scope'])) {
            $storeId = $this->storeManager->getStore($routeParams['_scope'])->getId();
        }

        if ($storeId != $this->storeManager->getStore()->getId()) {
            $routeParams['_scope_to_url'] = true;
        }

        if (empty($requestPath)) {
            $requestPath = BrandProcessor::BRAND_LIST_ROUTE_PATH;
        }

        if (isset($params['_request_path_only'])) {
            return $requestPath;
        }

        $routeParams['_direct'] = $requestPath;

        // Reset cached URL instance GET query params
        if (!isset($routeParams['_query'])) {
            $routeParams['_query'] = [];
        }

        return $this->getUrlInstance()->setScope($storeId)->getUrl($routePath, $routeParams);
    }

    /**
     * Retrieve URL Instance
     * @return UrlInterface
     */
    protected function getUrlInstance()
    {
        return $this->urlFactory->create();
    }
}
