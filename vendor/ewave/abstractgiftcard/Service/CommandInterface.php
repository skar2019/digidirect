<?php

namespace Ewave\AbstractGiftCard\Service;

use Ewave\AbstractGiftCard\Service\Command\CommandException;

/**
 * Interface CommandInterface
 * @package Ewave\AbstractGiftCard\Service
 * @api
 */
interface CommandInterface
{
    /**
     * Executes command basing on business object
     *
     * @param array $commandSubject
     * @return null|Command\ResultInterface
     * @throws CommandException
     */
    public function execute(array $commandSubject);
}
