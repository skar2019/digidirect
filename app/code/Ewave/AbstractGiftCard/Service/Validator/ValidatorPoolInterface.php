<?php

namespace Ewave\AbstractGiftCard\Service\Validator;

use Magento\Framework\Exception\NotFoundException;

/**
 * Interface ValidatorPoolInterface
 * @package Ewave\AbstractGiftCard\Service\Validator
 * @api
 */
interface ValidatorPoolInterface
{
    /**
     * Returns configured validator
     *
     * @param string $code
     * @return \Ewave\AbstractGiftCard\Service\Validator\ValidatorInterface
     * @throws NotFoundException
     */
    public function get($code);
}
