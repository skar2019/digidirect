<?php

namespace Ewave\Banner\Preference\Magento\Banner\Model\Banner;

use Ewave\Banner\Model\Cache;
use Ewave\Banner\Preference\Magento\Banner\Model\BannerModel;
use Magento\Customer\Model\Context;
use Ewave\Banner\Model\Attributes;
use Ewave\Banner\Inheritance\Magento\Banner\Model\Banner\Data as DataInheritance;

/**
 * Overwritten core file:
 * 1) added attributes
 * 2) added caching by customer group
 * 3) added caching by customer segments
 * 4) added caching by applied shopping cart price rules ids from quote
 */
class Data extends DataInheritance
{
    /**
     * @param array $bannersIds
     * @return array
     */
    protected function getBannersData($bannersIds)
    {
        $banners = [];
        foreach ($bannersIds as $bannerId) {
            if (!isset($this->banners[$bannerId])) {
                if (!$this->getBannerAttributesModel()->isAvailableForCustomerSegment($bannerId)) {
                    continue;
                }
                $content = $this->bannerResource->getStoreContent($bannerId, $this->storeId);
                /**
                 * @var $banner \Magento\Banner\Model\Banner
                 * Overwritten part: modified banner data array - added banner_images
                 */
                $this->bannerResource->load($this->banner, $bannerId);
                $this->banners[$bannerId] = [
                    'content' => $content ? $this->filterProvider->getPageFilter()->filter($content) : null,
                    'types' => $this->banner->getTypes(),
                    'id' => $bannerId,
                    'custom_attributes' => $this->_getAttributes($this->banner),
                ];
            }
            $banners[$bannerId] = $this->banners[$bannerId];
        }
        return array_filter($banners);
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $banners = $this->cacheManager->load($this->getCacheKey());
        if ($banners === false) {
            $salesRuleBanners = $this->getSalesRuleRelatedBanners();
            $this->cacheManager->save(
                $this->jsonComponent->encode($salesRuleBanners),
                self::SALES_RULE_RELATED_BANNERS_CACHE,
                [Cache::CACHE_TAG]
            );

            $banners = $this->jsonComponent->encode([
                'items' => [
                    self::BANNER_WIDGET_DISPLAY_SALESRULE => $salesRuleBanners,
                    self::BANNER_WIDGET_DISPLAY_CATALOGRULE => $this->getCatalogRuleRelatedBanners(),
                    self::BANNER_WIDGET_DISPLAY_FIXED => $this->getFixedBanners(),
                ],
                'store_id' => $this->storeId,
            ]);
            $this->cacheManager->save($banners, $this->getCacheKey(), [Cache::CACHE_TAG]);
        }
        return $this->jsonComponent->decode($banners);
    }

    /**
     * Public since 3.0.0 as it's used in blocks cache
     *
     * @return string
     */
    public function getCacheKey()
    {
        $cacheKey = $this->storeManager->getWebsite()->getId() .
            '_' .
            $this->httpContext->getValue(Context::CONTEXT_GROUP)
            . '_' .
            Cache::CACHE_TAG . $this->storeManager->getStore()->getCode();

        /**
         * Add customer segmentation feature to be working
         */
        $segmentIds = $this->customerSegment->getCustomerSegment();
        $segmentIds = $this->customerSegment->normalizeCustomerSegmentToString($segmentIds);
        $cacheKey .= '_CS_' . $segmentIds;

        /**
         * If there are no related banners to sales rules  it does not make sense to load quote
         *
         * @since 2.0.4
         */
        if ($this->needToCheckSalesRules()) {
            /**
             * Add cart price rules to be working
             */
            if ($this->checkoutSession->getQuoteId()) {
                $quote = $this->checkoutSession->getQuote();
                if ($quote && $quote->getAppliedRuleIds()) {
                    $cacheKey .= '_qrids' . $quote->getAppliedRuleIds();
                }
            }
        }
        return $cacheKey;
    }

    /**
     * @since 2.0.4
     * @return bool
     */
    protected function needToCheckSalesRules()
    {
        $salesRuleRelatedBanners = $this->cacheManager->load(self::SALES_RULE_RELATED_BANNERS_CACHE);
        if (false === $salesRuleRelatedBanners) {
            return true;
        }

        $salesRuleRelatedBanners = $this->jsonComponent->decode($salesRuleRelatedBanners);
        return !empty($salesRuleRelatedBanners);
    }

    /**
     * @param BannerModel $banner
     * @return []
     */
    protected function _getAttributes(BannerModel $banner)
    {
        return $this->getBannerAttributesModel()->getBannerAttributes($banner);
    }

    /**
     * @return Attributes
     */
    protected function getBannerAttributesModel()
    {
        if (null === $this->bannerAttributes) {
            $this->bannerAttributes = $this->bannerAttributesFactory->create();
        }

        return $this->bannerAttributes;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    protected function isNotInCache($data)
    {
        return $data === false;
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function prepareDataForCache($data)
    {
        return $this->jsonComponent->encode($data);
    }

    /**
     * @param string $data
     * @return mixed
     */
    protected function prepareDataFromCache($data)
    {
        return $this->jsonComponent->decode($data);
    }
}
