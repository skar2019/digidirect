<?php

namespace Ewave\AbstractGiftCard\Service\Http;

/**
 * Interface ConverterInterface
 * @package Ewave\AbstractGiftCard\Service\Http
 * @api
 */
interface ConverterInterface
{
    /**
     * Converts gateway response to ENV structure
     *
     * @param mixed $response
     * @return array
     * @throws \Ewave\AbstractGiftCard\Service\Http\ConverterException
     */
    public function convert($response);
}
