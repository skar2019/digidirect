<?php

namespace Marketplacer\Brand\Block\Brand;

use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Asset\GroupedCollection as PageAsset;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Config as ConfigHelper;
use Marketplacer\Brand\Helper\Url as UrlHelper;
use Marketplacer\Brand\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;

/**
 * Class View
 * @package Marketplacer\Brand\Block\Brand
 */
class View extends Template implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'marketplacer_brand_view';

    public const ASSET_CANONICAL = 'canonical';

    /**
     * @var BrandCollectionFactory
     */
    protected $brandCollectionFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

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
     * View constructor.
     * @param Template\Context $context
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param ConfigHelper $configHelper
     * @param UrlHelper $urlHelper
     * @param PageAsset $pageAsset
     * @param CategoryHelper $categoryHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        BrandCollectionFactory $brandCollectionFactory,
        ConfigHelper $configHelper,
        UrlHelper $urlHelper,
        PageAsset $pageAsset,
        CategoryHelper $categoryHelper,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->configHelper = $configHelper;
        $this->urlHelper = $urlHelper;
        $this->pageAsset = $pageAsset;
        $this->categoryHelper = $categoryHelper;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return BrandInterface | null
     */
    public function getCurrentBrand()
    {
        if (!$this->hasData('current_brand')) {
            $this->setData('current_brand', $this->coreRegistry->registry('current_brand'));
        }
        return $this->getData('current_brand');
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
        $brand = $this->getCurrentBrand();

        if ($brand) {
            $this->addBreadcrumbs($brand);

            $this->addPageMetaInfo($brand);

            if ($this->categoryHelper->canUseCanonicalTag()) {
                $this->addCanonicalUrl($brand);
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function addBreadcrumbs(BrandInterface $brand)
    {
        if (!$breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            return;
        }
        $storeId = $this->_storeManager->getStore()->getId();

        //home page crumb
        $breadcrumbsBlock->addCrumb('home', [
            'label' => __('Home'),
            'title' => __('Go to Home Page'),
            'link'  => $this->_storeManager->getStore()->getBaseUrl()
        ]);

        //listing crumb
        $listingTitle = $this->configHelper->getListingPageTitle($storeId) ?? __('Brands');

        $breadcrumbsBlock->addCrumb('brands', [
            'label' => $listingTitle,
            'title' => $listingTitle,
            'link'  => $this->urlHelper->getBrandListingUrl($storeId),
        ]);

        //brand crumb
        $breadcrumbsBlock->addCrumb('brand', [
            'label' => $brand->getName(),
            'title' => $brand->getName()
        ]);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function addPageMetaInfo(BrandInterface $brand)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        $listingTitle = $this->configHelper->getListingPageTitle($storeId);
        $brandTitle = $brand->getName();

        $this->pageConfig->getTitle()->prepend($listingTitle);
        $this->pageConfig->getTitle()->prepend($brandTitle);

        $brandMetaTitle = $brand->getMetaTitle() ?? $brandTitle;
        $brandMetaDescription = $brand->getMetaDescription() ?? $brandTitle;

        $this->pageConfig->setMetaTitle($brandMetaTitle);
        $this->pageConfig->setDescription($brandMetaDescription);

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->escapeHtml($brandTitle));
        }
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     */
    protected function addCanonicalUrl(BrandInterface $brand)
    {
        foreach ($this->pageAsset->getAll() as $url => $asset) {
            if ($asset->getContentType() == self::ASSET_CANONICAL) {
                $this->pageAsset->remove($url);
            }
        }

        $this->pageConfig->addRemotePageAsset(
            $this->urlHelper->getBrandUrl($brand),
            self::ASSET_CANONICAL,
            ['attributes' => ['rel' => self::ASSET_CANONICAL]]
        );
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getCurrentBrand()->getBrandId()];
    }
}
