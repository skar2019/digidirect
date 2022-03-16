<?php

namespace Marketplacer\Brand\Model;

use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Url as UrlHelper;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;

class BrandUrl implements \Marketplacer\BrandApi\Api\MarketplacerBrandUrlInterface
{
    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * @param UrlHelper $urlHelper
     */
    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param BrandInterface | MarketplacerBrandInterface $brand
     * @return string|null
     */
    public function getBrandUrl(MarketplacerBrandInterface $brand)
    {
        try {
            $url = $this->urlHelper->getBrandUrlById($brand->getBrandId(), $brand->getStoreId());
        } catch (\Throwable $e) {
            return null;
        }

        return $url;
    }
}
