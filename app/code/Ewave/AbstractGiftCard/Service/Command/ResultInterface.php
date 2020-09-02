<?php

namespace Ewave\AbstractGiftCard\Service\Command;

/**
 * Interface ResultInterface
 * @package Ewave\AbstractGiftCard\Service\Command
 * @api
 */
interface ResultInterface
{
    /**
     * Returns result interpretation
     *
     * @return mixed
     */
    public function get();
}
