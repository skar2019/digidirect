<?php

namespace Ewave\AbstractGiftCard\Service\Response;

/**
 * Interface HandlerInterface
 * @package Ewave\AbstractGiftCard\Service\Response
 * @api
 */
interface HandlerInterface
{
    /**
     * Handles response
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response);
}
