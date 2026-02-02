<?php
namespace Digidirect\EventbriteEvents\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\HTTP\Client\Curl;

class Eventbrite
{
    const CACHE_KEY = 'eventbrite_events';
    const CACHE_TTL = 3600;

    private $curl;
    private $cache;
    private $orgId;
    private $token;

    public function __construct(
        Curl $curl,
        CacheInterface $cache,
        $orgId,
        $token
    ) {
        $this->curl  = $curl;
        $this->cache = $cache;
        $this->orgId = $orgId;
        $this->token = $token;
    }

    public function getEvents()
    {
        if ($cached = $this->cache->load(self::CACHE_KEY)) {
            return json_decode($cached, true);
        }

        $live = $this->fetch('live');
        $past = $this->fetchAllPast();

        $data = [
            'live' => $live,
            'past' => $past
        ];

        $this->cache->save(
            json_encode($data),
            self::CACHE_KEY,
            [],
            self::CACHE_TTL
        );

        return $data;
    }

    private function fetch($status)
    {
        $url = "https://www.eventbriteapi.com/v3/organizations/{$this->orgId}/events/?status={$status}";
        return $this->call($url);
    }

    private function fetchAllPast()
    {
        $events = [];
        $url = "https://www.eventbriteapi.com/v3/organizations/{$this->orgId}/events/?status=ended";

        do {
            $data = $this->callRaw($url);
            $events = array_merge($events, $data['events'] ?? []);
            $url = $data['pagination']['has_more_items']
                ? $data['pagination']['continuation']
                : null;
        } while ($url);

        return $events;
    }

    private function call($url)
    {
        $data = $this->callRaw($url);
        return $data['events'] ?? [];
    }

    private function callRaw($url)
    {
        $this->curl->setHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ]);
        $this->curl->get($url);

        return json_decode($this->curl->getBody(), true);
    }
}
