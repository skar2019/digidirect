<?php

namespace Ewave\AbstractGiftCard\Service\Http;

use Ewave\AbstractGiftCard\Service\Response;

/**
 * Interface ClientInterface
 * @package Ewave\AbstractGiftCard\Service\Http
 * @api
 */
interface ClientInterface
{
    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param \Ewave\AbstractGiftCard\Service\Http\TransferInterface $transferObject
     * @return array
     * @throws \Ewave\AbstractGiftCard\Service\Http\ClientException
     * @throws \Ewave\AbstractGiftCard\Service\Http\ConverterException
     */
    public function placeRequest(\Ewave\AbstractGiftCard\Service\Http\TransferInterface $transferObject);
}
