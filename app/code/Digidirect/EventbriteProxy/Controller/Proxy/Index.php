<?php

namespace Digi\EventbriteProxy\Controller\Proxy;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\HTTP\Client\Curl;

class Index extends Action
{
    protected $resultJsonFactory;
    protected $curl;

    // 🔒 KEEP TOKEN HERE (NOT IN JS)
    const EVENTBRITE_TOKEN = '7OCEGMNMNZO6WLUFU2FM';
    const ORG_ID = '80988983007';

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Curl $curl
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->curl = $curl;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $type = $this->getRequest()->getParam('type', 'live');
        $continuation = $this->getRequest()->getParam('continuation');

        $url = "https://www.eventbriteapi.com/v3/organizations/" . self::ORG_ID . "/events/";
        $url .= "?status=" . ($type === 'past' ? 'ended' : 'live');

        if ($continuation) {
            $url .= "&continuation=" . urlencode($continuation);
        }

        try {
            $this->curl->addHeader("Authorization", "Bearer " . self::EVENTBRITE_TOKEN);
            $this->curl->get($url);

            $response = json_decode($this->curl->getBody(), true);

            return $result->setData($response ?: []);
        } catch (\Exception $e) {
            return $result->setData([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }
}
