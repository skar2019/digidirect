<?php

namespace Digidirect\EventbriteEvents\Controller\Api;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Events extends Action
{
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $orgId  = '80988983007';
        $token  = '7OCEGMNMNZO6WLUFU2FM';

        $status       = $this->getRequest()->getParam('status', 'live');
        $continuation = $this->getRequest()->getParam('continuation');

        $url = "https://www.eventbriteapi.com/v3/organizations/{$orgId}/events/?status={$status}";
        if ($continuation) {
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

        if ($response === false) {
            return $result->setHttpResponseCode(500)
                ->setData(['error' => 'Eventbrite API error']);
        }

        return $result->setHttpResponseCode($code)
            ->setJsonData($response);
    }
}
