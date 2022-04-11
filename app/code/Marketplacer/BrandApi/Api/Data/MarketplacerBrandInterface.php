<?php

namespace Marketplacer\BrandApi\Api\Data;

use Marketplacer\Base\Api\Data\DataObjectInterface;

interface MarketplacerBrandInterface extends DataObjectInterface
{
    public const BRAND_ID = 'brand_id';
    public const NAME = 'name';
    public const LOGO = 'logo';

    /**
     * Get Brand ID
     *
     * @return int|null
     */
    public function getBrandId();

    /**
     * @param int $brandId
     * @return self | MarketplacerBrandInterface
     */
    public function setBrandId($brandId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return self | MarketplacerBrandInterface
     */
    public function setName($name);

    /**
     * Get image
     *
     * @return string
     */
    public function getLogo();

    /**
     * @param string | null $logoUrl
     * @return self | MarketplacerBrandInterface
     */
    public function setLogo($logoUrl);
}
