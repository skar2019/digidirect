<?php

namespace Ewave\AbstractGiftCard\Service\Request;

/**
 * Interface BuilderInterface
 * @package Ewave\AbstractGiftCard\Service\Request
 * @api
 */
interface BuilderInterface
{
    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject);
}
