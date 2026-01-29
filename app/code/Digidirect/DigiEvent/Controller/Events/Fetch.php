<?php
namespace Digidirect\DigiEvent\Controller\Events;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\CacheInterface;

class Fetch extends Action
{
    protected $resultJsonFactory;
    protected $cache;

    private $orgId = '80988983007';
    private $token = '7OCEGMNMNZO6WLUFU2FM';
    private $cacheLifetime = 3600; // 1 hour cache

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CacheInterface $cache
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cache = $cache;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $type = $this->getRequest()->getParam('type', 'live'); // live or past

        $cacheKey = 'eventbrite_' . $type;
        $cached = $this->cache->load($cacheKey);
        if ($cached) {
            return $result->setData(json_decode($cached, true));
        }

        $status = $type === 'past' ? 'ended' : 'live';
        $url = "https://www.eventbriteapi.com/v3/organizations/{$this->orgId}/events/?status={$status}&token={$this->token}";

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                $data = ['success' => false, 'message' => $err];
            } else {
                $data = json_decode($response, true);
                $data = ['success' => true, 'events' => $data['events'] ?? []];

                // Sort past events
                if ($type === 'past') {
                    usort($data['events'], function($a, $b){
                        return strtotime($b['end']['local']) - strtotime($a['end']['local']);
                    });
                }
            }

            // Save to cache
            $this->cache->save(json_encode($data), $cacheKey, ['eventbrite_events'], $this->cacheLifetime);

            return $result->setData($data);

        } catch (\Exception $e) {
            $data = ['success' => false, 'message' => $e->getMessage()];
            return $result->setData($data);
        }
    }
}
