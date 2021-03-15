<?php

namespace Digidirect\MyOrderItemsGroups\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Sales\Model\Order\Item as SalesItem;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface;
use Digidirect\MyOrderItems\Helper\Data as ParentDataHelper;

/**
 * Class Data
 * @package Digidirect\MyOrderItemsGroups\Helper
 */
class Data extends ParentDataHelper
{
    /**
     * @var TimezoneInterface
     */
    protected $timeZone;

    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;

    /**
     * @var array 
     */
    protected $products = [];

    /**
     * Data constructor.
     * @param Config $eavConfig
     * @param ProductRepositoryInterface $productRepository
     * @param Context $context
     * @param LoggerInterface $logger
     * @param ImageHelper $imageHelper
     * @param ManagerInterface $eventManager
     * @param DataObjectFactory $dataObjectFactory
     * @param TimezoneInterface $timeZone
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        Config $eavConfig,
        ProductRepositoryInterface $productRepository,
        Context $context,
        LoggerInterface $logger,
        ImageHelper $imageHelper,
        ManagerInterface $eventManager,
        DataObjectFactory $dataObjectFactory,
        TimezoneInterface $timeZone,
        JsonSerializer $jsonSerializer
    ) {
        $this->timeZone = $timeZone;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct(
            $eavConfig,
            $productRepository,
            $context,
            $logger,
            $imageHelper,
            $eventManager,
            $dataObjectFactory
        );
    }

    /**
     * @param OrderItemGroupInterface $itemGroup
     */
    public function setUpdatedAt(OrderItemGroupInterface $itemGroup)
    {
        $updatedAt = $this->timeZone->date()->format('Y-m-d H:i:s');
        $itemGroup->setUpdatedAt($updatedAt);
    }

    /**
     * @param $data
     * @return string
     */
    public function jsonEncode($data)
    {
        return $this->jsonSerializer->serialize($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function jsonDecode($data)
    {
        return $this->jsonSerializer->unserialize($data);
    }

    /**
     * @param SalesItem $salesItem
     * @return bool
     */
    public function isItemSalable(SalesItem $salesItem)
    {
        if ($product = $salesItem->getProduct()) {
            $product = $this->getProduct($salesItem);
            if (!$product) {
                return false;
            }
            $info = $salesItem->getProductOptionByCode('info_buyRequest');
            $request = $this->dataObjectFactory->create($info);
            /** @var \Magento\Catalog\Model\Product $product */
            $cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced($request, $product);
            /**
             * Error message
             */
            if (is_string($cartCandidates) || $cartCandidates instanceof \Magento\Framework\Phrase) {
                return false;
            }
            /**
             * If prepare process return one object
             */
            if (!is_array($cartCandidates)) {
                $cartCandidates = [$cartCandidates];
            }
            foreach ($cartCandidates as $candidate) {
                if ($candidate instanceof \Magento\Catalog\Model\Product) {
                    /** @var \Magento\Catalog\Model\Product $candidate */
                    if (!$candidate->isSalable()) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param SalesItem $salesItem
     * @return \Magento\Catalog\Api\Data\ProductInterface|\Magento\Catalog\Model\Product|null
     */
    public function getProduct(SalesItem $salesItem)
    {
        if (!isset($this->products[$salesItem->getId()])) {
            $product = $salesItem->getProduct();
            if (!$product) {
                return null;
            }

            $productId = $product->getId();
            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $exception) {
                return null;
            }
            $this->products[$salesItem->getId()] = $product;
        }
        return $this->products[$salesItem->getId()];
    }
}
