<?php

namespace Digidirect\Navigation\Block;

/**
 * Interface MenuBlockInterface
 */
interface MenuBlockInterface
{
    /**
     * @return []
     */
    public function getMenuAsArray();

    /**
     * @return string
     */
    public function getMenuJsonArray();

    /**
     * @return string
     */
    public function getMenuJsonObject();
}
