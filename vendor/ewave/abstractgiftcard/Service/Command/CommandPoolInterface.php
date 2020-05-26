<?php

namespace Ewave\AbstractGiftCard\Service\Command;

use Magento\Framework\Exception\NotFoundException;
use Ewave\AbstractGiftCard\Service\CommandInterface;

/**
 * Interface CommandPoolInterface
 * @package Ewave\AbstractGiftCard\Service\Command
 * @api
 */
interface CommandPoolInterface
{
    /**
     * Retrieves operation
     *
     * @param string $commandCode
     * @return CommandInterface
     * @throws NotFoundException
     */
    public function get($commandCode);
}
