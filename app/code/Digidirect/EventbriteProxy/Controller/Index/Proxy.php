<?php
/**
 * Eventbrite Proxy Controller for Magento 2
 * 
 * Installation Instructions:
 * 1. Create directory: app/code/DigiDirect/EventbriteProxy
 * 2. Place this file at: app/code/DigiDirect/EventbriteProxy/Controller/Index/Proxy.php
 * 3. Create registration.php and module.xml (see separate files)
 * 4. Run: bin/magento setup:upgrade
 * 5. Run: bin/magento cache:flush
 * 
 * Usage: /eventbrite-proxy/index/proxy?status=live
 */

namespace DigiDirect\EventbriteProxy\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\HTTP\Client\Curl;

class Proxy extends Action
{
    protected $resultJsonFactory;
    protected $curl;
    
    // Your Eventbrite credentials
    const ORG_ID = '80988983007';
    const TOKEN = '7OCEGMNMNZO6WLUFU2FM';
    
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Curl $curl
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->curl = $curl;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        
        // Get parameters
        $status = $this->getRequest()->getParam('status', 'live'); // live or ended
        $continuation = $this->getRequest()->getParam('continuation', '');
        
        // Build Eventbrite API URL
        $url = sprintf(
            'https://www.eventbriteapi.com/v3/organizations/%s/events/?status=%s&token=%s',
            self::ORG_ID,
            $status,
            self::TOKEN
        );
        
        if (!empty($continuation)) {
            $url .= '&continuation=' . urlencode($continuation);
        }
        
        try {
            // Make API request
            $this->curl->setTimeout(30);
            $this->curl->get($url);
            
            $response = $this->curl->getBody();
            $httpCode = $this->curl->getStatus();
            
            if ($httpCode !== 200) {
                return $result->setData([
                    'error' => true,
                    'message' => 'API request failed with status: ' . $httpCode,
                    'events' => []
                ]);
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $result->setData([
                    'error' => true,
                    'message' => 'Invalid JSON response',
                    'events' => []
                ]);
            }
            
            // Return the data
            return $result->setData($data);
            
        } catch (\Exception $e) {
            return $result->setData([
                'error' => true,
                'message' => $e->getMessage(),
                'events' => []
            ]);
        }
    }
}