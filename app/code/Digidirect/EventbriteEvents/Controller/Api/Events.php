<?php

namespace Digidirect\EventbriteEvents\Controller\Api;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\CacheInterface;

class Events extends Action
{
    protected $resultJsonFactory;
    protected $cache;

    const CACHE_KEY = 'digidirect_eventbrite_events';
    const CACHE_LIFETIME = 3600; // 1 hour cache

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CacheInterface $cache
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cache = $cache;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $orgId = '80988983007';
        $token = '7OCEGMNMNZO6WLUFU2FM';

        $status = $this->getRequest()->getParam('status', 'live');
        $continuation = $this->getRequest()->getParam('continuation', '');

        // Cache key per status
        $cacheKey = self::CACHE_KEY . '_' . $status;

        if ($cached = $this->cache->load($cacheKey)) {
            return $result->setData(json_decode($cached, true));
        }

        $url = "https://www.eventbriteapi.com/v3/organizations/{$orgId}/events/?status={$status}";
        if (!empty($continuation)) {
            $url .= "&continuation=" . urlencode($continuation);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}"
            ]
        ]);

        $response = curl_exec($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $code != 200) {
            return $result->setHttpResponseCode(500)
                ->setData(['error' => 'Failed to fetch Eventbrite events']);
        }

        $this->cache->save($response, $cacheKey, ['DIGIDIRECT_EVENTS'], self::CACHE_LIFETIME);

        return $result->setData(json_decode($response, true));
    }
}
