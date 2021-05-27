<?php

namespace Ewave\Pronto\ProntoApi;

/**
 * Interface ResponseHandlerMultipleInterface
 * @package Ewave\Pronto\ProntoApi
 */
interface ResponseHandlerMultipleInterface extends ResponseHandlerInterface
{
    /**
     * @return mixed
     */
    public function finalize();
}
