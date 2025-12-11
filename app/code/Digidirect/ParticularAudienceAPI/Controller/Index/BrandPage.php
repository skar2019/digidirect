<?php

namespace Digidirect\ParticularAudienceAPI\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class BrandPage extends Action implements HttpPostActionInterface
{
    protected $_resultJsonFactory;
    protected $logger;
    protected $variable;
    protected $curl;
    protected $jsonSerializer;
    protected $cart;

    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Variable\Model\Variable $variable,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->variable = $variable;
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
        $this->cart = $cart;
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
        $brand = $this->getRequest()->getParam('brand');

        $variableData = $this->variable->loadByCode('pa_bearer_token');
        $bearerToken = $variableData->getValue('text');

        // Build customerId param
        $customerIdParam = $customerId ? "&customerId=" . $customerId : "";

        // Get SKUs in cart
        $items = $this->cart->getQuote()->getAllVisibleItems();
        $skuParams = '';
        foreach ($items as $index => $item) {
            $skuParams .= '&productsInCart[' . $index . ']=' . urlencode($item->getProduct()->getId());
        }
        
        $indexFilterValue = '&indexFilterValue=' . $brand;

        // Build recommendation URL
        $getRecommendationsUrl = "https://api-recs.particularaudience.com/3.0/recommendations?currentUrl=https://www.digidirect.com.au/pa-digi-brand" . $indexFilterValue ."&expandProductDetails=true";

        $this->logger->info("getRecommendationsUrl: " . $getRecommendationsUrl);

        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Authorization", "Bearer " . $bearerToken);
        $this->curl->get($getRecommendationsUrl);

        $getRecommendationsResult = $this->curl->getBody();
        $getRecommendationsResultJson = $this->jsonSerializer->unserialize($getRecommendationsResult);

        $result->setData($getRecommendationsResultJson);
        return $result;
    }
}
