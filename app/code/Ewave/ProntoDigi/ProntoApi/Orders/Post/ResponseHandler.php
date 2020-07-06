<?php

namespace Ewave\ProntoDigi\ProntoApi\Orders\Post;

use Ewave\AI\Model\Lib\Mapping\MapperInterface;
use Ewave\InvoiceIncrementId\Model\IncrementIdUpdater;
use Ewave\Pronto\ProntoApi\ResponseHandler as BaseResponseHandler;
use Ewave\Pronto\ProntoApi\ResponseHandlerInterface;
use Ewave\ProntoDigi\ProntoApi\Constants\CustomerAttributes;
use Ewave\ProntoDigi\ProntoApi\Constants\Order;
use Ewave\ProntoDigi\ProntoApi\Orders\Post;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;

class ResponseHandler extends BaseResponseHandler implements ResponseHandlerInterface
{
    const RESPONSE_ACCOUNT = 'account';
    const RESPONSE_ORDER_NO = 'order-no';
    const RESPONSE_ORDER_STATUS = 'order-status-code';
    const RESPONSE_ORDER_CREATED = 'order-created';
    const ORDER_CREATED_FLAG = 'Y';

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var OrderResource
     */
    protected $orderResource;

    /**
     * @var IncrementIdUpdater
     */
    protected $incrementIdUpdater;

    /**
     * ResponseHandler constructor.
     * @param OrderResource $orderResource
     * @param CustomerRepositoryInterface $customerRepository
     * @param IncrementIdUpdater $incrementIdUpdater
     * @param MapperInterface|null $mapper
     */
    public function __construct(
        OrderResource $orderResource,
        CustomerRepositoryInterface $customerRepository,
        IncrementIdUpdater $incrementIdUpdater,
        MapperInterface $mapper = null
    ) {
        $this->orderResource = $orderResource;
        $this->customerRepository = $customerRepository;
        $this->incrementIdUpdater = $incrementIdUpdater;
        parent::__construct($mapper);
    }

    /**
     * @param array $response
     * @return $this|BaseResponseHandler|ResponseHandlerInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function handle(array $response)
    {
        if (!isset($response[Order::ROOT_CONTAINER], $response[Order::ROOT_CONTAINER][Order::SALES_ORDER])) {
            throw new \Exception(__('Response structure is not valid'));
        }
        $data = $response[Order::ROOT_CONTAINER][Order::SALES_ORDER];

        if (isset($data[self::RESPONSE_ORDER_CREATED], $data[self::RESPONSE_ORDER_NO])
            && $data[self::RESPONSE_ORDER_CREATED] == self::ORDER_CREATED_FLAG) {
            /** @var OrderInterface|\Magento\Sales\Model\Order $order */
            $order = $this->process->getRunOption(Post::ORDER_RUN_OPTION_PARAMETER);

            if (!empty($data[self::RESPONSE_ACCOUNT]) && !$order->getCustomerIsGuest()) {
                $customer = $this->customerRepository->getById($order->getCustomerId());
                $customer->setData(CustomerAttributes::PRONTO_ACCOUNT_ID, $data[self::RESPONSE_ACCOUNT]);
                $customer->setCustomAttribute(CustomerAttributes::PRONTO_ACCOUNT_ID, $data[self::RESPONSE_ACCOUNT]);
                $this->customerRepository->save($customer);
            }

            $order->setData(Order::ATTRIBUTE_PRONTO_ORDER_NUMBER, $data[self::RESPONSE_ORDER_NO]);
            $this->orderResource->saveAttribute($order, Order::ATTRIBUTE_PRONTO_ORDER_NUMBER);

            if (!empty($data[self::RESPONSE_ORDER_STATUS])) {
                $order->setData(Order::ATTRIBUTE_PRONTO_STATUS_CODE, $data[self::RESPONSE_ORDER_STATUS]);
                $this->orderResource->saveAttribute($order, Order::ATTRIBUTE_PRONTO_STATUS_CODE);
            }

            /** @var \Magento\Sales\Model\Order\Invoice $invoice */
            $invoice = $order->getInvoiceCollection()->getFirstItem();
            $this->incrementIdUpdater->update($invoice, $data[self::RESPONSE_ORDER_NO]);
        } else {
            $this->logger->error(__('Order is not created in Pronto'));
        }

        return $this;
    }
}
