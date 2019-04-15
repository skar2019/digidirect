<?php

namespace Ewave\Pronto\ProntoApi;

use Ewave\AI\Model\Engine\Processor\ProcessorAbstract;
use Ewave\AI\Model\Lib\Connector\CurlHttpClient\RestApi;
use Ewave\AI\Model\Lib\Connector\CurlHttpClient\RestApiFactory;
use Ewave\AI\Model\Lib\Mapping\MapperInterface;
use Ewave\AI\Model\Lib\Mapping\MapperTemplateFilter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * Class RequestBuilder
 *
 * @package Ewave\Pronto\ProntoApi
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RequestBuilder implements RequestBuilderInterface
{
    const XML_PATH_API_ENDPOINT = 'ewave_pronto/api/endpoint';
    const XML_PATH_COMPANY_CODE = 'ewave_pronto/api/compcode';
    const XML_PATH_ACCEPT =       'ewave_pronto/api/accept';
    const XML_PATH_TOKEN =        'ewave_pronto/api/token';
    const XML_PATH_USER =         'ewave_pronto/api/user';

    /**
     * @var RestApiFactory
     */
    protected $restApiFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string
     */
    protected $requestMethodUriConfigPath;

    /**
     * @var string
     */
    protected $requestMethod;

    /**
     * @var array
     */
    protected $requestParams = [];

    /**
     * @var array
     */
    protected $queryParams = [];

    /**
     * @var array
     */
    protected $urlParams = [];

    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * @var Process
     */
    protected $process;

    /**
     * @var int
     */
    protected $storeId = Store::DEFAULT_STORE_ID;

    /**
     * @var RestApi
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $companyCode;

    /**
     * @var string
     */
    protected $accept;

    /**
     * @var bool
     */
    protected $isBuilt = false;

    /**
     * @var string
     */
    protected $requestMethodUrl;

    /**
     * @var string
     */
    protected $requestMethodUri;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * RequestBuilder constructor.
     * @param RestApiFactory $restApiFactory
     * @param string $requestMethodUriConfigPath
     * @param string $requestMethod
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param MapperInterface|null $mapper
     */
    public function __construct(
        RestApiFactory $restApiFactory,
        string $requestMethodUriConfigPath,
        string $requestMethod,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        MapperInterface $mapper = null
    ) {
        $this->restApiFactory = $restApiFactory;
        $this->requestMethodUriConfigPath = $requestMethodUriConfigPath;
        $this->requestMethod = $requestMethod;
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->mapper = $mapper;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function initRequestParams()
    {
        $this->requestParams = $this->process->getRunOptions();

        return $this;
    }

    /**
     * @param string|null $additionalMessage
     *
     * @return void
     * @throws \Exception
     */
    protected function throwInvalidRequestParamsException($additionalMessage = null)
    {
        $message = 'Invalid request params';
        if ($additionalMessage) {
            $message = ': ' . $additionalMessage;
        }
        throw new \Exception($message);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function init()
    {
        $scope = ScopeInterface::SCOPE_STORE;
        $scopeId = $this->storeId;
        $this->endpoint = $this->scopeConfig->getValue(self::XML_PATH_API_ENDPOINT, $scope, $scopeId);
        $this->requestMethodUri =  $this->scopeConfig->getValue($this->requestMethodUriConfigPath, $scope, $scopeId);
        if (!$this->endpoint || !$this->requestMethodUri) {
            throw new \Exception(__('Endpoint or Request method URI is empty.'));
        }
        $this->user = $this->scopeConfig->getValue(self::XML_PATH_USER, $scope, $scopeId);
        $this->companyCode = $this->scopeConfig->getValue(self::XML_PATH_COMPANY_CODE, $scope, $scopeId);
        $this->token = $this->encryptor->decrypt($this->scopeConfig->getValue(self::XML_PATH_TOKEN, $scope, $scopeId));

        if (!$this->user || !$this->token || !$this->companyCode) {
            throw new \Exception(__('User, Token or Company Code is empty.'));
        }

        return $this;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function mapData($data)
    {
        if ($this->mapper instanceof MapperInterface) {
            if ($this->mapper instanceof MapperTemplateFilter) {
                $this->mapper->setStoreId($this->storeId);
            }
            $data = $this->mapper->map($data);
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getHeaders()
    {
        return [
            'user' => $this->user,
            'token' => $this->token,
            'compcode' => $this->companyCode,
        ];
    }

    /**
     * @return array
     */
    protected function getRequestContent()
    {
        $variables = $this->requestParams;
        $transport = new DataObject();
        $transport->setVariables($variables);
        $variables = $transport->getVariables();

        return $this->mapData($variables);
    }

    /**
     * @param ProcessorAbstract $process
     *
     * @return $this
     * @throws \Exception
     */
    public function build(ProcessorAbstract $process)
    {
        /**
         * @var $httpClient RestApi
         */
        $this->process = $process;
        $this->initRequestParams();
        $this->init();

        $requestHeaders = $this->getHeaders();
        $requestContent = $this->getRequestContent();

        $httpClient = $this->httpClient = $this->restApiFactory->create(
            [
                'logger' => $process->getLogger(),
            ]
        );

        $url = $httpClient->getRestApiRequestUrl($this->endpoint, $this->requestMethodUri, $this->urlParams);
        $httpClient->setUri($url);
        $httpClient->setQuery($this->queryParams);
        $httpClient->setMethod($this->requestMethod);
        $httpClient->setHeaders($requestHeaders, ['user', 'token', 'compcode']);
        $httpClient->setContent($requestContent);

        /**
         * @var $adapter \Zend\Http\Client\Adapter\Curl
         */
        $zendHttpClient = $this->httpClient->getHttpClient();
        $adapter = $zendHttpClient->getAdapter();
        $adapter->setCurlOption(CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $adapter->setCurlOption(CURLOPT_DNS_USE_GLOBAL_CACHE, false);

        $this->isBuilt = true;

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function throwNotBuiltExceptionIfNotBuilt()
    {
        if (!$this->isBuilt) {
            throw new \Exception('Request was not built');
        }

        return $this;
    }

    /**
     * @return RestApi
     * @throws \Exception
     */
    public function getHttpClient()
    {
        $this->throwNotBuiltExceptionIfNotBuilt();

        return $this->httpClient;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getStoreId()
    {
        $this->throwNotBuiltExceptionIfNotBuilt();

        return $this->storeId;
    }
}
