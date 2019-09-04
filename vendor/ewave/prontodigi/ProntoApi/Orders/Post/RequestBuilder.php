<?php

namespace Ewave\ProntoDigi\ProntoApi\Orders\Post;

use Ewave\AI\Model\Lib\Connector\CurlHttpClient\RestApiFactory;
use Ewave\AI\Model\Lib\Mapping\MapperInterface;
use Ewave\Pronto\ProntoApi\RequestBuilder as BaseRequestBuilder;
use Ewave\Pronto\ProntoApi\RequestBuilderInterface;
use Ewave\ProntoDigi\ProntoApi\Constants\Order;
use Ewave\ProntoDigi\ProntoApi\Constants\Order\PaymentDetails as PaymentDetail;
use Ewave\ProntoDigi\ProntoApi\Orders\Post;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\OfflinePayments\Model\Banktransfer;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository;

class RequestBuilder extends BaseRequestBuilder implements RequestBuilderInterface
{
    const XML_PATH_API_ORDER_INTERFACE = 'ewave_pronto/api_order/order_post_uri';

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderPostExecutor
     */
    protected $orderPostExecutor;

    /**
     * RequestBuilder constructor.
     * @param RestApiFactory $restApiFactory
     * @param string $requestMethodUriConfigPath
     * @param string $requestMethod
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param OrderRepository $orderRepository
     * @param OrderPostExecutor $orderPostExecutor
     * @param MapperInterface|null $mapper
     */
    public function __construct(
        RestApiFactory $restApiFactory,
        string $requestMethodUriConfigPath,
        string $requestMethod,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        OrderRepository $orderRepository,
        OrderPostExecutor $orderPostExecutor,
        MapperInterface $mapper = null
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderPostExecutor = $orderPostExecutor;
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
     * @return array
     */
    public function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function initRequestParams()
    {
        $requestParameters = $this->process->getRunOptions();
        $order = $requestParameters[Post::ORDER_RUN_OPTION_PARAMETER] ?? null;
        if (is_numeric($order)) {
            $order = $this->orderRepository->get($order);
        }

        if (!$order instanceof OrderInterface) {
            throw new \Exception('Invalid request params');
        }
        $this->orderPostExecutor->validate($order);
        $this->process->addProcessIdentifiersToLog(
            [
                'order_id' => $order->getId(),
                OrderInterface::INCREMENT_ID => $order->getIncrementId(),
            ]
        );

        $this->process->setRunOption(Post::ORDER_RUN_OPTION_PARAMETER, $order);
        $requestParameters[Post::ORDER_RUN_OPTION_PARAMETER] = $order;
        $this->requestParams = $requestParameters;
        $this->storeId = $order->getStoreId();

        return $this;
    }

    /**
     * @return string
     */
    protected function getRequestContent()
    {
        /**
         * @var $order \Magento\Sales\Model\Order
         */
        $data = parent::getRequestContent();

        $order = $this->process->getRunOption(Post::ORDER_RUN_OPTION_PARAMETER);
        $paymentInstance = $order->getPayment();

        if ($paymentInstance->getMethod() == Banktransfer::PAYMENT_METHOD_BANKTRANSFER_CODE) {
            unset($data[Order::SALES_ORDER][Order::HEADER][Order::PAYMENT_DETAILS][PaymentDetail::PAYMENT_DETAIL]);
        }

        $xml = \Ewave\AI\Model\Lib\Adapter\Import\Xml::assocToXml($data, Order::ROOT_CONTAINER);
        return $xml;
    }
}
