<?php

namespace Ewave\Banner\Inheritance\Magento\Banner\Model\Banner;

use Ewave\Banner\Component\Json;
use Ewave\Banner\Model\Cache;
use Ewave\Banner\Model\CustomerSegment;
use Magento\Banner\Model\ResourceModel\Banner as BannerResourceModel;
use Magento\Banner\Model\Banner as BannerModel;
use Magento\Banner\Model\Config;
use Ewave\Banner\Model\Attributes;
use Ewave\Banner\Model\AttributesFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Banner\Model\Banner\Data as MagentoBannerData;

/**
 * Class to control differences in EE and CE versions
 */
class Data extends MagentoBannerData
{
    const BANNERS_CACHE_KEY = 'ewave_banners_cache';
    const SALES_RULE_RELATED_BANNERS_CACHE = 'sales_rule_related_banners_cache';
    const BANNER_WIDGET_DISPLAY_SALESRULE = Config::BANNER_WIDGET_DISPLAY_SALESRULE;
    const BANNER_WIDGET_DISPLAY_CATALOGRULE = Config::BANNER_WIDGET_DISPLAY_CATALOGRULE;
    const BANNER_WIDGET_DISPLAY_FIXED = Config::BANNER_WIDGET_DISPLAY_FIXED;

    /**
     * @var Attributes
     */
    protected $bannerAttributes;

    /**
     * @var Cache
     */
    protected $cacheManager;

    /**
     * @var AttributesFactory
     */
    protected $bannerAttributesFactory;

    /**
     * @var CustomerSegment
     */
    protected $customerSegment;

    /**
     * @var Json
     */
    protected $jsonComponent;

    /**
     * Data constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param BannerResourceModel $bannerResource
     * @param BannerModel $banner
     * @param StoreManagerInterface $storeManager
     * @param HttpContext $httpContext
     * @param FilterProvider $filterProvider
     * @param AttributesFactory $bannerAttributesFactory
     * @param CustomerSegment $customerSegment
     * @param Cache $cacheManager
     * @param Json|null $jsonComponent
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        BannerResourceModel $bannerResource,
        BannerModel $banner,
        StoreManagerInterface $storeManager,
        HttpContext $httpContext,
        FilterProvider $filterProvider,
        AttributesFactory $bannerAttributesFactory,
        CustomerSegment $customerSegment,
        Cache $cacheManager,
        Json $jsonComponent = null
    ) {
        parent::__construct($checkoutSession, $bannerResource, $banner, $storeManager, $httpContext, $filterProvider);
        $this->bannerAttributesFactory = $bannerAttributesFactory;
        $this->cacheManager = $cacheManager;
        $this->customerSegment = $customerSegment;
        if (null === $jsonComponent) {
            $jsonComponent = ObjectManager::getInstance()->get(Json::class);
        }

        $this->jsonComponent = $jsonComponent;
    }
}
