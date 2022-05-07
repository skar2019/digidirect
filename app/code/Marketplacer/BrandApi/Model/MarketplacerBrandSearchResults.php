<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Marketplacer\BrandApi\Model;

use Magento\Framework\Api\Search\SearchResult;

/**
 * Class MarketplacerBrandSearchResult
 */
class MarketplacerBrandSearchResults extends SearchResult implements \Marketplacer\BrandApi\Api\Data\MarketplacerBrandSearchResultsInterface
{
    /**
     * Get brand list.
     *
     * @return \Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface[]
     */
    public function getBrands()
    {
        return $this->getItems();
    }

    /**
     * Set brand list.
     *
     * @param \Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface[] $brands
     * @return $this
     */
    public function setBrands(array $brands = null)
    {
        return $this->setItems($brands);
    }
}
