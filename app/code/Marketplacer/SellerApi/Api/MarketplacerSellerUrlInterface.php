<?php

namespace Marketplacer\SellerApi\Api;

interface MarketplacerSellerUrlInterface
{
    /**
     * @param Data\MarketplacerSellerInterface $seller
     * @return string|null
     */
    public function getSellerUrl(Data\MarketplacerSellerInterface $seller);
}
