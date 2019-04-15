<?php

namespace Ewave\ProntoDigi\ProntoApi\Orders\Get;

use Ewave\AI\Model\Lib\Connector\CurlHttpClient\RestApiFactory;
use Ewave\AI\Model\Lib\Mapping\MapperInterface;
use Ewave\Pronto\ProntoApi\RequestBuilder as BaseRequestBuilder;
use Ewave\Pronto\ProntoApi\RequestBuilderInterface;
use Ewave\ProntoDigi\ProntoApi\Constants\Order;
use Ewave\ProntoDigi\ProntoApi\Constants\Order as OrderConst;
use Ewave\ProntoDigi\ProntoApi\Orders\Get;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class RequestBuilder extends BaseRequestBuilder implements RequestBuilderInterface
{
    const XML_PATH_API_ORDER_INTERFACE = 'ewave_pronto/api_order/order_get_uri';
    const CALL_PARAM_ORDER_NO = 'order-no';

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * RequestBuilder constructor.
     * @param RestApiFactory $restApiFactory
     * @param $requestMethodUriConfigPath
     * @param $requestMethod
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param OrderRepositoryInterface $orderRepository
     * @param MapperInterface|null $mapper
     */
    public function __construct(
        RestApiFactory $restApiFactory,
        string $requestMethodUriConfigPath,
        string $requestMethod,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        OrderRepositoryInterface $orderRepository,
        MapperInterface $mapper = null
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct(
            $restApiFactory,
            $requestMethodUriConfigPath,
            $requestMethod,
            $scopeConfig,
            $encryptor,
            $mapper
        );
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function initRequestParams()
    {
        $requestParameters = $this->process->getRunOptions();
        $order = $requestParameters[Get::ORDER_RUN_OPTION_PARAMETER] ?? null;
        if (is_numeric($order)) {
            $order = $this->orderRepository->get($order);
        }

        if (!$order instanceof OrderInterface) {
            throw new \Exception('Invalid request params');
        }

        $prontoOrderNumber = $order->getData(OrderConst::ATTRIBUTE_PRONTO_ORDER_NUMBER);

        if (!$prontoOrderNumber) {
            throw new \Exception(
                'Pronto Order Number is required, Order Increment ID: %1',
                $order->getIncrementId()
            );
        }

        $this->process->addProcessIdentifiersToLog(
            [
                'order_id' => $order->getId(),
                OrderInterface::INCREMENT_ID => $order->getIncrementId(),
            ]
        );

        $this->process->setRunOption(Get::ORDER_RUN_OPTION_PARAMETER, $order);
        $requestParameters[Get::ORDER_RUN_OPTION_PARAMETER] = $order;
        $this->requestParams = $requestParameters;
        $this->storeId = $order->getStoreId();
        $this->queryParams[self::CALL_PARAM_ORDER_NO] = $prontoOrderNumber;

        return $this;
    }
}
