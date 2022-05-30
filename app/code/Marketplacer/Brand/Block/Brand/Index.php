<?php

namespace Marketplacer\Brand\Block\Brand;

use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Asset\GroupedCollection as PageAsset;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Config as ConfigHelper;
use Marketplacer\Brand\Helper\Url as UrlHelper;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;

class Index extends Template
{
    public const ASSET_CANONICAL = 'canonical';

    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $attributeRetriever;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * @var PageAsset
     */
    protected $pageAsset;

    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /**
     * Brand constructor.
     * @param Template\Context $context
     * @param BrandRepositoryInterface $brandRepository
     * @param StoreManagerInterface $storeManager
     * @param BrandAttributeRetrieverInterface $attributeRetriever
     * @param ConfigHelper $configHelper
     * @param UrlHelper $urlHelper
     * @param PageAsset $pageAsset
     * @param CategoryHelper $categoryHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        BrandRepositoryInterface $brandRepository,
        StoreManagerInterface $storeManager,
        BrandAttributeRetrieverInterface $attributeRetriever,
        ConfigHelper $configHelper,
        UrlHelper $urlHelper,
        PageAsset $pageAsset,
        CategoryHelper $categoryHelper,
        array $data = []
    ) {
        $this->brandRepository = $brandRepository;
        $this->storeManager = $storeManager;
        $this->attributeRetriever = $attributeRetriever;
        $this->configHelper = $configHelper;
        $this->urlHelper = $urlHelper;
        $this->pageAsset = $pageAsset;
        $this->categoryHelper = $categoryHelper;

        parent::__construct($context, $data);
    }

    /**
     * @return BrandInterface[]
     * @throws NoSuchEntityException
     */
    public function getAllDisplayedBrands()
    {
        if (!$this->hasData('all_brands')) {
            $currentStoreId = $this->storeManager->getStore()->getId();

            $this->setData('all_brands', $this->brandRepository->getAllDisplayedBrands($currentStoreId));
        }

        return $this->getData('all_brands');
    }

    /**
     * Prepare global layout
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        $this->addBreadcrumbs();

        $this->addPageMetaInfo();

        if ($this->categoryHelper->canUseCanonicalTag()) {
            $this->addCanonicalUrl();
        }

        return parent::_prepareLayout();
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function addBreadcrumbs()
    {
        if (!$breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            return;
        }
        $storeId = $this->_storeManager->getStore()->getId();

        $breadcrumbsBlock->addCrumb('home', [
            'label' => __('Home'),
            'title' => __('Go to Home Page'),
            'link'  => $this->_storeManager->getStore()->getBaseUrl()
        ]);
        $listingTitle = $this->configHelper->getListingPageTitle($storeId) ?? __('Brands');

        $breadcrumbsBlock->addCrumb('brands', [
            'label' => $listingTitle,
            'title' => $listingTitle
        ]);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function addPageMetaInfo()
    {
        $storeId = $this->_storeManager->getStore()->getId();

        $listingTitle = $this->configHelper->getListingPageTitle($storeId);

        $this->pageConfig->getTitle()->set($listingTitle);

        $listingMetaTitle = $this->configHelper->getListingMetaTitle() ?? $listingTitle;
        $listingMetaDescription = $this->configHelper->getListingMetaDescription() ?? $listingTitle;

        $this->pageConfig->setMetaTitle($listingMetaTitle);
        $this->pageConfig->setDescription($listingMetaDescription);

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->escapeHtml($listingTitle));
        }
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     */
    protected function addCanonicalUrl()
    {
        foreach ($this->pageAsset->getAll() as $url => $asset) {
            if ($asset->getContentType() == self::ASSET_CANONICAL) {
                $this->pageAsset->remove($url);
            }
        }

        $this->pageConfig->addRemotePageAsset(
            $this->urlHelper->getBrandListingUrl(),
            self::ASSET_CANONICAL,
            ['attributes' => ['rel' => self::ASSET_CANONICAL]]
        );
    }
}
