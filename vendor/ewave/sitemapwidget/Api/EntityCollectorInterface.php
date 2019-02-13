<?php

namespace Ewave\SitemapWidget\Api;

interface EntityCollectorInterface
{
    /**
     * @param array $data
     * @param array $accessKeys
     * @return mixed
     */
    public function collect(array &$data, array $accessKeys);
}
