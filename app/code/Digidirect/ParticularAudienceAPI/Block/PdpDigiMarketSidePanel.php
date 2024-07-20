<?php
namespace Digidirect\ParticularAudienceAPI\Block;

class PdpDigiMarketSidePanel extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
    
    protected $variable;
    
    protected $curl;
    
    protected $jsonSerializer;
    
    protected $logger;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,    
        
        \Magento\Framework\Registry $registry,
        \Magento\Variable\Model\Variable $variable,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {        
        $this->_registry = $registry;
        $this->variable = $variable;
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }
    
    public function _prepareLayout() {
        return parent::_prepareLayout();
    }
    
    public function getCurrentProduct(){         
        return $this->_registry->registry('current_product');
    }
    
    public function getRecommendedProducts(){
        
        //Get token from custom variable
        $variableData = $this->variable->loadByCode('pa_bearer_token');
        $bearerToken = $variableData->getValue('text');
        
        /*$getTokenUrl = '';
        $getTokenParams = ["grant_type"=>"password","username"=>"sfdc.connect@digidirect.com.au","password"=>"idv5EdQ3cNYG1zuF3pje!inXRgbsxaaQRzbjWCnllpWZ0z","client_id"=>"3MVG9wt4IL4O5wvKHkw4LwXtVE2s.EYz9zxXLdFQ_F5LhhQQ9dRSWJEvkcyWje6OFpVm3qOLjsWVBjJVUy26z","client_secret"=>"CEEF6DD5884CF7C8DA8089015A1438F089B9B729A2DA0CEC9F63E1003B63D9B9"];
        
        $this->curl->addHeader("Content-Type", "application/x-www-form-urlencoded");
        $this->curl->post($getTokenUrl, $getTokenParams);

        $getTokenResult = $this->curl->getBody();

        $getTokenJson = $this->jsonSerializer->unserialize($getTokenResult);
        
        //$this->logger->info("getTokenJson['access_token']: " . $getTokenJson['access_token']); */
        
        $getRecommendationsUrl = 'https://api-recs.particularaudience.com/3.0/recommendations?currentUrl=https://www.digidirect.com.au/pa-digi-products-pdp&expandProductDetails=false';
        
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Authorization", "Bearer " . $bearerToken);
        $this->curl->get($getRecommendationsUrl);
        
        $getRecommendationsResult = $this->curl->getBody();
        $getRecommendationsResultJson = $this->jsonSerializer->unserialize($getRecommendationsResult);
        
        $slots = $getRecommendationsResultJson['recommendations']['route']['widgets'][0]['slots'];
        
        foreach($slots as $key=>$value) {
            $this->logger->info("key: " . $key . ", productId: " . $value['products']['refId']); 
        }
        
        //$this->logger->info("Response: " . $webSignUpResult); 
        return $getRecommendationsResultJson;
    }
    
}