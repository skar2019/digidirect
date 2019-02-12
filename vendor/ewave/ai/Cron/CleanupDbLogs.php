<?php

namespace Ewave\AI\Cron;

class CleanupDbLogs
{
    /**
     * @var \Ewave\AI\Helper\Logger
     */
    protected $loggerHelper;

    /**
     * @var \Ewave\AI\Model\ResourceModel\Logger\Logger
     */
    protected $loggerResource;

    /**
     * LogBackup constructor.
     * @param \Ewave\AI\Helper\Logger $loggerHelper
     * @param \Ewave\AI\Model\ResourceModel\Logger\Logger $loggerResource
     */
    public function __construct(
        \Ewave\AI\Helper\Logger $loggerHelper,
        \Ewave\AI\Model\ResourceModel\Logger\Logger $loggerResource
    ) {
        $this->loggerHelper = $loggerHelper;
        $this->loggerResource = $loggerResource;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $days = $this->loggerHelper->getRemoveDbRecordsOlderThanDays();
        if ($days > 0) {
            $this->loggerResource->removeRecordsOlderThanDays($days);
        }
    }
}
