<?php

namespace Marketplacer\BrandApi\Api;

interface MarketplacerBrandUrlInterface
{
    /**
     * @param Data\MarketplacerBrandInterface $seller
     * @return string|null
     */
    public function getBrandUrl(Data\MarketplacerBrandInterface $seller);
}
