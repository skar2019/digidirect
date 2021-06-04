<?php

namespace Ewave\CriteoOneTag\Block;

/**
 * Class Product
 *
 * @package Ewave\CriteoOneTag\Block
 */
class Product extends AbstractTag
{
    /**
     * @return string
     */
    public function getItem()
    {
        return $this->getStringItem(
            $this->getDataType(),
            $this->serializer->serialize($this->registry->registry('product')->getSku())
        );
    }
}
