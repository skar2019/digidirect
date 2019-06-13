<?php

namespace Ewave\ProntoDigi\ProntoApi\Orders\Get;

use Ewave\AI\Model\Lib\Mapping\MapperInterface;
use Ewave\Pronto\ProntoApi\ResponseHandler as BaseResponseHandler;
use Ewave\Pronto\ProntoApi\ResponseHandlerInterface;
use Ewave\ProntoDigi\ProntoApi\Constants\Order;
use Ewave\ProntoDigi\ProntoApi\Orders\Post;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;

class ResponseHandler extends BaseResponseHandler implements ResponseHandlerInterface
{
    const RESPONSE_SUCCESS_STATUS = 'success';
    const ORDER_TRACKING_NUMBER_PARAM = 'so-consignment-note';
    const MANIFEST_NUMBER_PARAM = 'so-user-only-alpha20-1';

    /**
     * @var OrderResource
     */
    protected $orderResource;

    /**
     * ResponseHandler constructor.
     * @param OrderResource $orderResource
     * @param MapperInterface|null $mapper
     */
    public function __construct(
        OrderResource $orderResource,
        MapperInterface $mapper = null
    ) {
        $this->orderResource = $orderResource;
        parent::__construct($mapper);
    }

    /**
     * @param array $response
     * @return BaseResponseHandler|ResponseHandlerInterface|void
     * @throws \Exception
     */
    public function handle(array $response)
    {
        if (!isset($response[Order::SALES_ORDER], $response[Order::SALES_ORDER][Order::HEADER])
            || empty($response[Order::SALES_ORDER]['response']['status'])
        ) {
            throw new \Exception(__('Response structure is not valid'));
        }

        if ($response[Order::SALES_ORDER]['response']['status'] != self::RESPONSE_SUCCESS_STATUS) {
            throw new \Exception(__('Response status is not success'));
        }

        $data = $response[Order::SALES_ORDER][Order::HEADER];
        /** @var OrderInterface|\Magento\Sales\Model\Order $order */
        $order = $this->process->getRunOption(Post::ORDER_RUN_OPTION_PARAMETER);

        if (!empty($data[self::ORDER_TRACKING_NUMBER_PARAM])) {
            $order->setData(Order::ATTRIBUTE_PRONTO_ORDER_TRACKING_NUMBER, $data[self::ORDER_TRACKING_NUMBER_PARAM]);
            $this->orderResource->saveAttribute($order, Order::ATTRIBUTE_PRONTO_ORDER_TRACKING_NUMBER);
        } else {
            $this->logger->info(
                __('Cannot update Order Tracking Number, %1 is empty', self::ORDER_TRACKING_NUMBER_PARAM)
            );
        }

        if (!empty($data[self::MANIFEST_NUMBER_PARAM])) {
            $order->setData(Order::ATTRIBUTE_PRONTO_MANIFEST_NUMBER, $data[self::MANIFEST_NUMBER_PARAM]);
            $this->orderResource->saveAttribute($order, Order::ATTRIBUTE_PRONTO_MANIFEST_NUMBER);
        } else {
            $this->logger->info(
                __('Cannot update Order Manifest Number, %1 is empty', self::MANIFEST_NUMBER_PARAM)
            );
        }

    }
}
