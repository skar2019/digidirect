<?php

namespace Ewave\AI\Cron;

use Ewave\AI\Model\Logger\Types\File\BackupInterface;
use Ewave\AI\Model\Engine\Mail\MailFactory;

/**
 * Class LogBackup
 * @package Ewave\AI\Cron
 */
class LogBackup
{
    /**
     * @var BackupInterface
     */
    protected $backup;

    /**
     * @var MailFactory
     */
    protected $mailer;

    /**
     * LogBackup constructor.
     * @param BackupInterface $backup
     * @param MailFactory $mailer
     */
    public function __construct(
        BackupInterface $backup,
        MailFactory $mailer
    ) {
        $this->backup = $backup;
        $this->mailer = $mailer;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        try {
            $this->backup->backup();
        } catch (\Throwable $e) {
            $msg = __('Logs backup creation error: %1', $e->getMessage());
            $this->mailer->create()->sendFailEmail($msg, null, true);
        }
        return $this;
    }
}
