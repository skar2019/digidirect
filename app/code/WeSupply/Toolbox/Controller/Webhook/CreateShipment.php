<?php

namespace WeSupply\Toolbox\Controller\Webhook;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;

class CreateShipment extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    private $orderItemFactory;

    /**
     * @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface
     */
    private $getSourceItemsBySku;

    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    private $convertOrder;

    /**
     * @var \Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory
     */
    private $shipmentTrackFactory;

    /**
     * CreateShipment constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     * @param \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItemsBySku
     * @param \Magento\Sales\Model\Convert\Order $convertOrder
     * @param \Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory $shipmentTrackFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItemsBySku,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory $shipmentTrackFactory
    )
    {
        $this->resultJsonFactory = $jsonFactory;
        $this->orderFactory = $orderFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->getSourceItemsBySku = $getSourceItemsBySku;
        $this->convertOrder = $convertOrder;
        $this->shipmentTrackFactory = $shipmentTrackFactory;

        parent::__construct($context);
    }

    /**
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        $params = $this->getRequest()->getParams();
        $order = $this->orderFactory->create()->load($params['orderId']);

        if (!$order->canShip()) {
            throw new LocalizedException(__('You can\'t create shipment for this order (already shipped).'));
        }

        $qtyToShip = $params['itemQty'];
        $itemToShip = $this->orderItemFactory->create()->load($params['itemId']);

        $sourceCode = 'default';
        $productSku = $itemToShip->getProduct()->getSku();
        $inventorySource = $this->getSourceItemsBySku->execute($productSku);

        foreach ($inventorySource as $source) {
            if ($source->getQuantity() > 0) {
                $sourceCode = $source->getSourceCode();
            }
        }

        $shipmentItem = $this->convertOrder->itemToShipmentItem($itemToShip)->setQty($qtyToShip);

        // Check if order item has qty to ship or is virtual
        if (!$itemToShip->getQtyToShip() || $itemToShip->getIsVirtual()) {
            throw new LocalizedException(__('You can\'t create shipment for this item (already shipped).'));
        }

        $shipment = $this->convertOrder->toShipment($order);
        $shipment->addItem($shipmentItem);

        // Register shipment
        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);

        $shipment->getExtensionAttributes()->setSourceCode($sourceCode);

        $track = $this->shipmentTrackFactory->create()
            ->addData([
                'carrier_code' => $params['carrierCode'],
                'title' => $params['carrierTitle'],
                'number' => $params['trackingNumber'],
            ]);

        $shipment->addTrack($track);

        try {
            // Save created shipment and order
            $shipment->save();
            $shipment->getOrder()->save();

            return $resultJson->setData([
                'success' => TRUE,
                'shipment id' => $shipment->getIncrementId()
            ]);

        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
