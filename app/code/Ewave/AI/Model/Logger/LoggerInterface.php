<?php
namespace Ewave\AI\Model\Logger;

use Ewave\AI\Model\Integrations\Integrations as Integration;

/**
 * Interface LoggerInterface
 *
 * @package Ewave\AI\Model\Logger
 */
interface LoggerInterface extends \PSR\Log\LoggerInterface
{
    /**
     * @param string $logType
     * @return mixed
     */
    public function getLogRecordIdentifier($logType);
}
