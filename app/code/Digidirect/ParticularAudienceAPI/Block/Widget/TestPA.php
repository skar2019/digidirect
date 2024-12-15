<?php

namespace Digidirect\ParticularAudienceAPI\Block\Widget;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;

class TestPA extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $customer;
    
    protected $_template = 'Digidirect_ParticularAudienceAPI::widget/test-pa.phtml';
    
    public const PA_CUSTOMER_ID = "pa_customer_id";
    
    public const PA_SESSION_ID = "pa_session_id";
    
    protected $_cookieManager;
    
    protected $_cookieMetadataFactory;
    
    protected $_sessionManager;
    
    protected $variable;
    
    protected $curl;
    
    protected $jsonSerializer;
  
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Magento\Customer\Model\Session $customerSession,   
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Variable\Model\Variable $variable,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        array $data = []
    ) {        
        $this->customer = $customerSession;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_sessionManager = $sessionManager;
        $this->variable = $variable;
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context, $data);
    }
    
    public function checkCustomer() {
        return $this->customer;
    }
    
    public function getCookie($cookie) {
        return $this->_cookieManager->getCookie($cookie);
    }
    
    public function getBearerToken() {
        $variableData = $this->variable->loadByCode('pa_bearer_token');
        $bearerToken = $variableData->getValue('text');
        return $bearerToken;
    }
    
     public function getRecommendations(){
        $customerId = $this->getCookie(self::PA_CUSTOMER_ID);
        $bearerToken = $this->getBearerToken();
        
        if ($customerId) {
            $customerIdParam = "&customerId=".$customerId;
        } else {
            $customerIdParam = "";
        }

        $getRecommendationsUrl = "https://api-recs.particularaudience.com/3.0/recommendations?currentUrl=https://www.digidirect.com.au/pa-digi-home-page&expandProductDetails=true".$customerIdParam;
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Authorization", "Bearer " . $bearerToken);
        $this->curl->get($getRecommendationsUrl);

        $getRecommendationsResult = $this->curl->getBody();
        $getRecommendationsResultJson = $this->jsonSerializer->unserialize($getRecommendationsResult);
        
        return $getRecommendationsResultJson;

    }
    
    
}