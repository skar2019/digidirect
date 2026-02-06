<?php

namespace Digidirect\EventbriteEvents\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;

class Data extends AbstractHelper
{
    protected $curl;

    // 🔑 Put your Eventbrite token here
    private $token = '7OCEGMNMNZO6WLUFU2FM';
    
    // 👉 Replace with your organisation ID
    private $orgId = '80988983007';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Curl $curl
    ) {
        $this->curl = $curl;
        parent::__construct($context);
    }

    public function getEvents()
    {
        try {

            $url = "https://www.eventbriteapi.com/v3/organizations/{$this->orgId}/events/?status=live";

            $this->curl->addHeader("Authorization", "Bearer {$this->token}");

            $this->curl->get($url);

            $response = json_decode($this->curl->getBody(), true);

            return $response['events'] ?? [];

        } catch (\Exception $e) {
            return [];
        }
    }
}
