<?php

namespace Digidirect\EventbriteEvents\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public function getEvents()
    {
        return [
            [
                "title" => "Test Event 1",
                "date" => "2026-02-06"
            ],
            [
                "title" => "Test Event 2",
                "date" => "2026-02-07"
            ]
        ];
    }
}
