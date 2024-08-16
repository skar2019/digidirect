<?php

namespace Digidirect\ParticularAudienceAPI\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class TestRecommendation extends Action implements HttpPostActionInterface {
    
    private $checkoutSession;

    private $cartRepository;

    private $productRepository;

    private $json;

    private $configurableType;
    
    protected $_resultJsonFactory;

    protected $logger;
    
    protected $formKey;
    
    protected $cart;
    
    protected $productItem;

    protected $variable;
    
    protected $curl;

    protected $jsonSerializer;
    
    public function __construct(
        Context $context,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\Product $productItem,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Variable\Model\Variable $variable,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->json = $json;
        $this->configurableType = $configurableType;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->productItem = $productItem;
        $this->logger = $logger;
        $this->variable = $variable;
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context);
    }

    /**
    * @return ResultInterface
    * @throws LocalizedException
    */
    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        $customerId = $this->getRequest()->getParam('customerId');
        
        $variableData = $this->variable->loadByCode('pa_bearer_token');
        $bearerToken = $variableData->getValue('text');
        
        if ($customerId) {
            $customerIdParam = "&customerId=".$customerId;
        } else {
            $customerIdParam = "";
        }

        $getRecommendationsUrl = "https://api-recs.particularaudience.com/3.0/recommendations?currentUrl=https://www.digidirect.com.au/home-page-pa&expandProductDetails=true".$customerIdParam;
        //$this->logger->info("getRecommendationsUrl: " . $getRecommendationsUrl);ß
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Authorization", "Bearer " . $bearerToken);
        $this->curl->get($getRecommendationsUrl);

        $getRecommendationsResult = $this->curl->getBody();
        $getRecommendationsResultJson = $this->jsonSerializer->unserialize($getRecommendationsResult);

        $result->setData($getRecommendationsResultJson);
        return $result;
    }
}
