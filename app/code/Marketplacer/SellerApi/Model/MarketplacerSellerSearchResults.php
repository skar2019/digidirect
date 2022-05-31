<?php
declare(strict_types=1);

namespace Marketplacer\SellerApi\Model;

use Magento\Framework\Api\Search\SearchResult;

/**
 * Class MarketplacerBrandSearchResult
 */
class MarketplacerSellerSearchResults extends SearchResult implements \Marketplacer\SellerApi\Api\Data\MarketplacerSellerSearchResultsInterface
{
    /**
     * Get seller list.
     *
     * @return \Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface[]
     */
    public function getSellers()
    {
        return $this->getItems();
    }

    /**
     * Set seller list.
     *
     * @param \Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface[] $sellers
     * @return $this
     */
    public function setSellers(array $sellers = null)
    {
        return $this->setItems($sellers);
    }
}
