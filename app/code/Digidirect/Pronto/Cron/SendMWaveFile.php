<?php
namespace Digidirect\Pronto\Cron;

use Digidirect\Pronto\Helper\SftpMWaveSender;

class SendMWaveFile
{

    public function __construct(
        SftpMWaveSender $helper)
    {
        $this->helper = $helper;
    }

    public function execute()
    {
        $this->helper->sendFile();
    }

    public function sendExtraData()
    {
        $this->helper->sendExtraData();
    }
}
