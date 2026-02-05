<?php

namespace igidirect\EventbriteEvents\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public function getEvents()
    {
        return [
            "events" => [
                [
                    "name" => ["text" => "Sample Magento Event"],
                    "start" => ["local" => "2026-02-05"]
                ]
            ]
        ];
    }
}
