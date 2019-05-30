<?php
declare(strict_types=1);
/**
 * Ewave
 *
 */
namespace Ewave\Digi\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Ewave\ProntoDigi\ProntoApi\Orders\Post\MapperHelper as ProntoHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class ProntoAdditionalInfo
 * @package Ewave\Digi\Block\Adminhtml
 */
class ProntoAdditionalInfo extends Template
{

    const ADDITIONAL_DATA_TITLE = [
        'Order Tracking Number',
        'Pronto Order Number',
        'Pronto Manifest Number',
        'Rep',
    ];
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var ProntoHelper
     */
    private $prontoHelper;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ProntoAdditionalInfo constructor.
     * @param Template\Context $context
     * @param JsonSerializer $jsonSerializer
     * @param OrderRepositoryInterface $orderRepository
     * @param ProntoHelper $prontoHelper
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        JsonSerializer $jsonSerializer,
        OrderRepositoryInterface $orderRepository,
        ProntoHelper $prontoHelper,
        LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonSerializer = $jsonSerializer;
        $this->orderRepository = $orderRepository;
        $this->prontoHelper = $prontoHelper;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface|null
     */
    public function getCurrentOrder(): ?OrderInterface
    {
        $order = null;
        $orderId = $this->getOrderId();
        if ($orderId && !empty($orderId)) {
            try {
                $order = $this->orderRepository->get($orderId);
            } catch (NoSuchEntityException $e) {
                $this->logger->warning('Additional Pronto Attribute '. $e->getMessage(), ['orderId' => $orderId]);
            }
        }
        return $order;
    }

    /**
     * @return array
     */
    public function getOrderData(): array
    {
        $orderData = [];
        $currentOrder = $this->getCurrentOrder();
        if ($currentOrder) {
            $orderData[] = $currentOrder->getProntoOrderTrackingNumber();
            $orderData[] = $currentOrder->getProntoOrderNumber();
            $orderData[] = $currentOrder->getProntoManifestNumber();
            $orderData[] = $this->prontoHelper->getRep($currentOrder);
            $orderData = array_combine(self::ADDITIONAL_DATA_TITLE, $orderData);
        }
        return array_filter($orderData, function ($attrVal) {
            return !empty($attrVal);
        });
    }

    /**
     * Retrieves current order Id.
     *
     * @return integer
     */
    private function getOrderId(): int
    {
        return (int) $this->getRequest()->getParam('order_id');
    }
}
