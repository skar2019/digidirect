<?php

namespace Ewave\AbstractGiftCard\Service\Http;

/**
 * Interface TransferFactoryInterface
 * @package Ewave\AbstractGiftCard\Service\Http
 * @api
 */
interface TransferFactoryInterface
{
    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @param null $storeId
     * @return mixed
     */
    public function create(array $request, $storeId = null);
}