<?php

namespace Digidirect\ParticularAudienceAPI\Block;


class TestPdp extends \Magento\Framework\View\Element\Template
{
    protected $productCollectionFactory;
    
    protected $variable;
    
    protected $curl;
    
    protected $jsonSerializer;
    
    protected $logger;
    
    protected $cookieManager;
    
    protected $cookieMetadataFactory;
    
    protected $_productRepository;
  
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,    
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Variable\Model\Variable $variable,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {        
        $this->productCollectionFactory = $productCollectionFactory;
        $this->variable = $variable;
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
        $this->logger = $logger;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->listProductBlock = $listProductBlock;
        $this->_productRepository = $productRepository;
        parent::__construct($context, $data);
    }
    
    public function getProductById($id) {
        $product = $this->_productRepository->getById($id);
        return $product;
    }
    
    public function getBearerToken() {
        $variableData = $this->variable->loadByCode('pa_bearer_token');
        $bearerToken = $variableData->getValue('text');
        return $bearerToken;
    }
    
    public function getProductPrice($product){
        return $this->listProductBlock->getProductPrice($product);
    }
    
    public function getAddToCartPostParams($product){
        return $this->listProductBlock->getAddToCartPostParams($product);
    }
    
    public function runJs($output) {
        echo "<script>".json_encode($output)."</script>";
    }
}