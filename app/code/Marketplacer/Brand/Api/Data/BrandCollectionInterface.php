<?php

namespace Marketplacer\Brand\Api\Data;

interface BrandCollectionInterface
{
    /**
     * @param int | string $brandId
     * @return $this
     */
    public function addBrandIdToFilter($brandId);

    /**
     * @param string $name
     * @return $this
     */
    public function addBrandNameToFilter($name);

    /**
     * @return $this
     */
    public function addWithNameToFilter();
}
