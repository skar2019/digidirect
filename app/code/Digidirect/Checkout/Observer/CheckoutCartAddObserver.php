<?php

namespace Digidirect\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\App\Helper\AbstractHelper;

class CheckoutCartAddObserver extends AbstractHelper implements \Magento\Framework\Event\ObserverInterface
{
    protected $curl;
    
    protected $jsonSerializer;
   
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
    }
    
    /**
     * execute
     *
     * @param EventObserver observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $getTokenUrl = 'https://digidirect2022.my.salesforce.com/services/oauth2/token';
        $getTokenParams = ["grant_type"=>"password","username"=>"sfdc.connect@digidirect.com.au","password"=>"idv5EdQ3cNYG1zuF3pje!inXRgbsxaaQRzbjWCnllpWZ0z","client_id"=>"3MVG9wt4IL4O5wvKHkw4LwXtVE2s.EYz9zxXLdFQ_F5LhhQQ9dRSWJEvkcyWje6OFpVm3qOLjsWVBjJVUy26z","client_secret"=>"CEEF6DD5884CF7C8DA8089015A1438F089B9B729A2DA0CEC9F63E1003B63D9B9"];
        
        $getTokenCurl = $this->curl;
        $getTokenCurl->addHeader("Content-Type", "application/x-www-form-urlencoded");
        $getTokenCurl->post($getTokenUrl, $getTokenParams);

        $getTokenResult = $getTokenCurl->getBody();

        $getTokenJson = $this->jsonSerializer->unserialize($getTokenResult);
        echo $this->console_log('$getTokenJson: ' . json_encode($getTokenJson));
        echo $this->console_log('access_token: ' . $getTokenJson['access_token']);
        
        $webSignUpUrl = 'https://digidirect2022.my.salesforce.com/services/apexrest/WebSignup';
        $webSignUpParams = ["email"=>"rondel@kayweb.com.au","source"=>"Web"];

        $webSignUpCurl = $this->curl;
        $webSignUpCurl->addHeader("Content-Type", "application/JSON");
        $webSignUpCurl->addHeader("Authorization", "Bearer " . $getTokenJson['access_token']);
        $webSignUpCurl->post($webSignUpUrl, $webSignUpParams);
        
        $webSignUpResult = $webSignUpCurl->getBody();
        
        $getSignUpResultJson = $this->jsonSerializer->unserialize($webSignUpResult);
        echo $this->console_log('$getSignUpResultJson: ' . json_encode($getSignUpResultJson));
    }
    
    function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
            ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }
}