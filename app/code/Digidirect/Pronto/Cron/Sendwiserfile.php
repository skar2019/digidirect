<?php
namespace Digidirect\Pronto\Cron;

use Digidirect\Pronto\Helper\Sftpwisersender;

class Sendwiserfile
{

    public function __construct(
        Sftpfilesender $helper)
    {
        $this->helper = $helper;
    }

    public function execute()
    {
        $this->helper->sendFile();
    }
}
