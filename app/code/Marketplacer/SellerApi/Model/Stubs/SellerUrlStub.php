<?php

namespace Marketplacer\SellerApi\Model\Stubs;

use Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface;

class SellerUrlStub implements \Marketplacer\SellerApi\Api\MarketplacerSellerUrlInterface
{
    /**
     * @param MarketplacerSellerInterface $seller
     * @return null
     */
    public function getSellerUrl(MarketplacerSellerInterface $seller)
    {
        return null;
    }
}
