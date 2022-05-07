<?php

namespace Marketplacer\BrandApi\Model;

use Magento\Framework\Model\AbstractModel;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;

class MarketplacerBrand extends AbstractModel implements MarketplacerBrandInterface
{
    /**
     * Get brand identifier
     *
     * @return int | string | null
     */
    public function getBrandId()
    {
        return $this->_getData(MarketplacerBrandInterface::BRAND_ID);
    }

    /**
     * Set brand identifier
     *
     * @param int $brandId
     * @return self | MarketplacerBrandInterface
     */
    public function setBrandId($brandId)
    {
        $this->setData(MarketplacerBrandInterface::BRAND_ID, $brandId);
        return $this;
    }

    /**
     * Get Brand name
     *
     * @return int|string|null
     */
    public function getName()
    {
        return $this->_getData(MarketplacerBrandInterface::NAME);
    }

    /**
     * @param string $name
     * @return self|MarketplacerBrandInterface
     */
    public function setName($name)
    {
        $this->setData(MarketplacerBrandInterface::NAME, $name);
        return $this;
    }

    /**
     * Get brand logo url
     *
     * @return int|string|null
     */
    public function getLogo()
    {
        return $this->_getData(MarketplacerBrandInterface::LOGO);
    }

    /**
     * Set brand logo url
     *
     * @param string|null $logoUrl
     * @return $this|MarketplacerBrandInterface
     */
    public function setLogo($logoUrl)
    {
        $this->setData(MarketplacerBrandInterface::LOGO, $logoUrl);
        return $this;
    }
}
