<?php

namespace Marketplacer\BrandApi\Model\Stubs;

use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;

class BrandUrlStub implements \Marketplacer\BrandApi\Api\MarketplacerBrandUrlInterface
{
    /**
     * @param MarketplacerBrandInterface $seller
     * @return null
     */
    public function getBrandUrl(MarketplacerBrandInterface $seller)
    {
        return null;
    }
}
