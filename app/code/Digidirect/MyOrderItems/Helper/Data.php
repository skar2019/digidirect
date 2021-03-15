<?php

namespace Digidirect\MyOrderItems\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Psr\Log\LoggerInterface;
use Digidirect\MyOrderItems\Api\OrderItemStateRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Data
 * @package Digidirect\MyOrderItems\Helper
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends AbstractHelper
{
    /**
     * Attribute keys constants
     */
    const LABEL = 'label';
    const VALUE = 'value';

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var OrderItemStateRepositoryInterface
     */
    protected $orderItemStateRepository;

    /**
     * @var array
     */
    protected $additionalAttributes;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var TimezoneInterface
     */
    protected $timeZone;

    /**
     * Data constructor.
     * @param Config $eavConfig
     * @param ProductRepositoryInterface $productRepository
     * @param Context $context
     * @param LoggerInterface $logger
     * @param ImageHelper $imageHelper
     * @param ManagerInterface $eventManager
     * @param DataObjectFactory $dataObjectFactory
     * @param OrderItemStateRepositoryInterface|null $orderItemStateRepository
     * @param array $additionalAttributes
     * @param PricingHelper|null $pricingHelper
     * @param TimezoneInterface|null $timeZone
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Config $eavConfig,
        ProductRepositoryInterface $productRepository,
        Context $context,
        LoggerInterface $logger,
        ImageHelper $imageHelper,
        ManagerInterface $eventManager,
        DataObjectFactory $dataObjectFactory,
        OrderItemStateRepositoryInterface $orderItemStateRepository = null,
        array $additionalAttributes = [],
        PricingHelper $pricingHelper = null,
        TimezoneInterface $timeZone = null
    ) {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->imageHelper = $imageHelper;
        $this->eventManager = $eventManager;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->eavConfig = $eavConfig;
        parent::__construct($context);
        $this->orderItemStateRepository = $orderItemStateRepository ?: ObjectManager::getInstance()
            ->get(OrderItemStateRepositoryInterface::class);
        $this->additionalAttributes = $additionalAttributes;
        $this->pricingHelper = $pricingHelper ?: ObjectManager::getInstance()->create(PricingHelper::class);
        $this->timeZone = $timeZone ?: ObjectManager::getInstance()->create(TimezoneInterface::class);
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return \Magento\Catalog\Api\Data\ProductInterface|\Magento\Catalog\Model\Product|null
     */
    public function getProduct(\Magento\Sales\Model\Order\Item $orderItem)
    {
        $product = $orderItem->getProduct();
        if (!$product) {
            return null;
        }
        $productId = $product->getId();
        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $exception) {
            return null;
        }
        return $product;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $salesItem
     * @param string $imageType
     * @return string
     */
    public function getImageFilePath(
        \Magento\Sales\Model\Order\Item $salesItem,
        $imageType = 'product_thumbnail_image'
    ) {
        $product = $this->getProduct($salesItem) ?: $this->dataObjectFactory->create();
        $imageUrl = $this->imageHelper->init($product, $imageType)->getUrl();
        $dataObject = $this->getImageDataObject($salesItem->getItemId(), $imageUrl);
        $this->eventManager->dispatch('sales_item_image_process', ['data_object' => $dataObject]);
        return $dataObject->getImageUrl();
    }

    /**
     * @param int $salesItemId
     * @param string $imageUrl
     * @return \Magento\Framework\DataObject
     */
    protected function getImageDataObject($salesItemId, $imageUrl)
    {
        $dataObject = $this->dataObjectFactory->create();
        $dataObject->setSaleItemId($salesItemId);
        $dataObject->setImageUrl($imageUrl);
        return $dataObject;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $salesItem
     * @param array $attributes
     * @return array
     */
    public function getAttributes(\Magento\Sales\Model\Order\Item $salesItem, array $attributes)
    {
        $product = $this->getProduct($salesItem) ?: $this->dataObjectFactory->create();
        $eavAttributes = $this->eavConfig->getEntityAttributes(Product::ENTITY, $product);
        $attributeValues = [];
        /** @var AbstractAttribute $eavAttribute */
        foreach ($eavAttributes as $eavAttribute) {
            if (in_array($eavAttribute->getAttributeCode(), $attributes)) {
                if ($product->hasData($eavAttribute->getAttributeCode())) {
                    try {
                        $value = (string)$eavAttribute->getFrontend()->getValue($product);
                    } catch (\Throwable $e) {
                        $value = null;
                    }
                } else {
                    $value = null;
                }

                $attributeValues[$eavAttribute->getAttributeCode()][self::VALUE]
                    = $value;
                $attributeValues[$eavAttribute->getAttributeCode()][self::LABEL]
                    = $eavAttribute->getDefaultFrontendLabel();
            }
        }

        if (!empty($this->additionalAttributes)) {
            $orderItem = $this->orderItemStateRepository->getById($salesItem->getItemId());
            foreach ($attributes as $attribute) {
                if (!array_key_exists($attribute, $attributeValues)
                    && array_key_exists($attribute, $this->additionalAttributes)
                    && $orderItem->hasData($attribute)
                ) {
                    $attributeValues[$attribute][self::VALUE] = $this->prepareProductValue(
                        $attribute,
                        $orderItem->getData($attribute)
                    );
                    $attributeValues[$attribute][self::LABEL] = $this->additionalAttributes[$attribute];
                }
            }
        }

        $dataObject = $this->getProductDataObject($salesItem->getItemId(), $attributeValues);
        $this->eventManager->dispatch('sales_item_data_process', ['data_object' => $dataObject]);
        $attributeValues = $dataObject->getAttributeValues();

        foreach ($attributeValues as $attributeCode => &$attributeValue) {
            $value = $this->prepareProductAttributeValue($attributeCode, $attributeValue[self::VALUE]);
            $attributeValue[self::VALUE] = $value;
        }

        return $attributeValues;
    }

    /**
     * @param string $attributeCode
     * @param string $attributeValue
     * @return string
     */
    protected function prepareProductAttributeValue($attributeCode, $attributeValue)
    {
        try {
            $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);
            return $this->prepareProductValue($attribute->getFrontendInput(), $attributeValue);
        } catch (\Throwable $e) {
            return $attributeValue;
        }
    }

    /**
     * @param int $salesItemId
     * @param array $values
     * @return \Magento\Framework\DataObject
     */
    protected function getProductDataObject($salesItemId, array $values)
    {
        $dataObject = $this->dataObjectFactory->create();
        $dataObject->setSaleItemId($salesItemId);
        $dataObject->setAttributeValues($values);
        return $dataObject;
    }

    /**
     * @return mixed|null
     */
    public function getAdditionalAttributes()
    {
        return $this->additionalAttributes;
    }

    /**
     * @param string $itemName
     * @param string $value
     * @return string
     */
    protected function prepareProductValue($itemName, $value)
    {
        if (null !== $value) {
            switch ($itemName) {
                case 'price':
                    $value = $this->pricingHelper->currency($value, true, false);
                    break;
                case 'date_of_purchase':
                    $value = $this->timeZone->date($value)->format('Y-m-d H:i:s');
                    break;
            }
        }
        return $value;
    }
}
