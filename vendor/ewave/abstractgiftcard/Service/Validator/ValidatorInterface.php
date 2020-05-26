<?php

namespace Ewave\AbstractGiftCard\Service\Validator;

/**
 * Interface ValidatorInterface
 * @package Ewave\AbstractGiftCard\Service\Validator
 * @api
 */
interface ValidatorInterface
{
    /**
     * Performs domain-related validation for business object
     *
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject);
}
