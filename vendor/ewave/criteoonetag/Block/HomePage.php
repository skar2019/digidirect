<?php

namespace Ewave\CriteoOneTag\Block;

/**
 * Class HomePage
 *
 * @package Ewave\CriteoOneTag\Block
 */
class HomePage extends AbstractTag
{
    /**
     * @return string
     */
    public function getItem()
    {
        return $this->getStringItem($this->getDataType());
    }
}
