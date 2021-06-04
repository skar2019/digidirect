<?php

namespace Ewave\SEO\Model\Router;

/**
 * Interface PathsUpdaterInterface
 *
 * @package Ewave\SEO\Model\Router
 */
interface PathsUpdaterInterface
{
    /**
     * @param array $paths
     *
     * @return array
     */
    public function update(array $paths);
}
