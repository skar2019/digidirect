<?php

namespace Ewave\PreOrder\Helper;

use Ewave\PreOrder\Api\Data\ProductAttributeInterface;
use Ewave\PreOrder\Model\Preorder\Mapper as PreorderMapper;
use Ewave\PreOrder\Model\StockResolver;
use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Helper\Context;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Data
 *
 * @package Ewave\PreOrder\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const BACKORDERS_PREORDER_OPTION = 111;

    /**
     * @var \Ewave\PreOrder\Helper\Config
     */
    protected $preOrderConfigHelper;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Ewave\PreOrder\Helper\Templater
     */
    protected $templater;

    /**
     * @var \Ewave\PreOrder\Model\Preorder\Mapper
     */
    protected $preOrderMapper;

    /**
     * @var \Magento\CatalogInventory\Api\StockItemRepositoryInterface
     */
    protected $stockItemRepository;

    /**
     * @var StockResolver
     */
    protected $stockResolver;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var string
     */
    protected $availabilityDateTag;

    /**
     * Preorder constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Ewave\PreOrder\Helper\Config $preOrderConfigHelper
     * @param \Ewave\PreOrder\Helper\Templater $templater
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param StockResolver $stockResolver
     * @param \Ewave\PreOrder\Model\Preorder\Mapper $preOrderMapper
     * @param StockItemRepositoryInterface $stockItemRepository
     * @param TimezoneInterface $localeDate
     * @param string $availabilityDateTag
     */
    public function __construct(
        Context $context,
        Config $preOrderConfigHelper,
        Templater $templater,
        StockRegistryInterface $stockRegistry,
        StockResolver $stockResolver,
        PreorderMapper $preOrderMapper,
        StockItemRepositoryInterface $stockItemRepository,
        TimezoneInterface $localeDate = null,
        string $availabilityDateTag = null
    ) {
        $this->preOrderConfigHelper = $preOrderConfigHelper;
        $this->templater = $templater;
        $this->stockRegistry = $stockRegistry;
        $this->stockResolver = $stockResolver;
        $this->preOrderMapper = $preOrderMapper;
        $this->stockItemRepository = $stockItemRepository;
        $this->localeDate = $localeDate ?: ObjectManager::getInstance()->get(TimezoneInterface::class);
        $this->availabilityDateTag = $availabilityDateTag;

        parent::__construct($context);
    }

    /**
     * Get config helper
     *
     * @return \Ewave\PreOrder\Helper\Config
     */
    public function getConfig()
    {
        return $this->preOrderConfigHelper;
    }

    /**
     * Get quote item PreOrder note
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return string
     */
    public function getQuoteItemPreorderNote(\Magento\Quote\Model\Quote\Item $quoteItem)
    {
        if ($quoteItem->getProductType() == Configurable::TYPE_CODE) {
            $option = $quoteItem->getOptionByCode('simple_product');
            $simpleProduct = $option->getProduct();
            return $this->getProductPreorderNote($simpleProduct);
        } elseif ($quoteItem->getProductType() == Bundle::TYPE_CODE) {
            foreach ($quoteItem->getChildren() as $childItem) {
                if ($this->isQuoteItemPreorder($childItem)) {
                    return $this->getProductPreorderNote($childItem->getProduct());
                }
            }
        } else {
            return $this->getProductPreorderNote($quoteItem->getProduct());
        }
    }

    /**
     * Get product PreOrder note
     *
     * @param Product $product
     * @return string
     */
    public function getProductPreorderNote(Product $product)
    {
        $note = '';
        try {
            $stockItem = $this->getStockProvider($product);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            return $note;
        }

        if ($this->verifyStockItem($stockItem)) {
            $template = $product->getData(ProductAttributeInterface::CODE_PREORDER_NOTE);
            if (null === $template) {
                $template = $this->getAttributeRawValue($product, ProductAttributeInterface::CODE_PREORDER_NOTE);
            }

            if (!$template) {
                $template = $this->getConfig()->getDefaultPreorderNote();
            }

            $note = $this->processTemplate($product, $template);
            $note = $this->insertProductAvailabilityDate($product, $note);
        }
        return $note;
    }

    /**
     * Get product PreOrder cart label
     *
     * @param Product $product
     * @return string
     */
    public function getProductPreorderCartLabel(Product $product)
    {
        $template = $product->getData(ProductAttributeInterface::CODE_PREORDER_CART_LABEL);
        if (null === $template) {
            $template = $this->getAttributeRawValue($product, ProductAttributeInterface::CODE_PREORDER_CART_LABEL);
        }

        if (!$template) {
            $template = $this->getConfig()->getDefaultPreorderCartLabel();
        }

        $note = $this->processTemplate($product, $template);
        return $note;
    }

    /**
     * Get quote item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return bool
     */
    public function isQuoteItemPreorder(\Magento\Quote\Model\Quote\Item $item)
    {
        if (null === $item->getIsPreorder()) {
            try {
                $preOrderModel = $this->preOrderMapper->getOrderItemPreorder($item);
                $result = $preOrderModel->isQuoteItemPreorder($item);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $result = false;
            }
            $item->setIsPreorder($result);
        }
        return $item->getIsPreorder();
    }

    /**
     * Whether product is PreOrder
     *
     * @param Product $product
     * @param int|null $requiredQty
     * @return bool
     */
    public function isProductPreorder(Product $product, $requiredQty = null)
    {
        if (null === $product->getIsPreorder()) {
            try {
                $preOrderModel = $this->preOrderMapper->getProductPreorder($product);
                $result = $preOrderModel->isProductPreorder($product, $requiredQty);
            } catch (\Exception $e) {
                $result = false;
            }
            $product->setIsPreorder($result);
        }

        return $product->getIsPreorder();
    }

    /**
     * Get raw attribute value
     *
     * @param Product $product
     * @param string $attrCode
     * @return string
     */
    public function getAttributeRawValue(Product $product, $attrCode)
    {
        $template = $product->getResource()
            ->getAttributeRawValue($product->getId(), $attrCode, $product->getStoreId());
        return $template;
    }

    /**
     * Whether order item is PreOrder
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return bool
     */
    public function isOrderItemPreorder(\Magento\Sales\Model\Order\Item $orderItem)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $orderItem->getProduct();
        if (!$result = $this->isProductPreorder($product, 0)) {
            foreach ($orderItem->getChildrenItems() as $childItem) {
                $result = $this->isOrderItemPreorder($childItem);
                if ($result) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Check stock item qty
     *
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return bool
     */
    public function checkStockItemQty(\Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem)
    {
        $isPreOrderEnabled = $this->getConfig()->preordersEnabled();
        $isPreorder = self::BACKORDERS_PREORDER_OPTION == $stockItem->getBackorders();
        $isStockAllowed = $this->verifyStockItem($stockItem);

        $result = $isPreOrderEnabled && $isPreorder && $isStockAllowed;
        return $result;
    }

    /**
     * Verify stock item
     *
     * @param object $stockItem
     * @return bool
     */
    public function verifyStockItem($stockItem)
    {
        if ($stockItem instanceof \Magento\Framework\DataObject
            && ($stockItem->getQty() <= $stockItem->getMinQty()
                || !$stockItem->getData('is_in_stock')
                && self::BACKORDERS_PREORDER_OPTION == $stockItem->getBackorders())
        ) {
            return $this->getConfig()->isAllowEmptyQty();
        }

        return true;
    }

    /**
     * Check is required qty less then product inventory availability
     *
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param int $qty
     * @return bool
     */
    public function checkQtyAvailability(\Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem, $qty)
    {
        return $qty <= $stockItem->getQty() || ($qty >= $stockItem->getQty() && $this->getConfig()->isAllowEmptyQty());
    }

    /**
     * Process template
     *
     * @param Product $product
     * @param string $template
     * @return string
     */
    protected function processTemplate(Product $product, $template)
    {
        return $this->templater->process($template, $product);
    }

    /**
     * Get stock provider
     *
     * @param object $product
     * @return mixed
     */
    public function getStockProvider($product)
    {
        return $this->stockResolver->getProductStockItem($product);
    }

    /**
     * Replace tag with date
     *
     * @param Product $product
     * @param string $note
     * @return string
     */
    protected function insertProductAvailabilityDate(Product $product, $note)
    {
        $availabilityDate = $this->getAttributeRawValue(
            $product,
            ProductAttributeInterface::CODE_PRODUCT_AVAILABILITY_DATE
        );

        if ($availabilityDate
            && !empty($this->availabilityDateTag)
            && strpos($note, $this->availabilityDateTag) !== false
        ) {
            $date = $this->localeDate->formatDate(
                new \DateTime($availabilityDate),
                \IntlDateFormatter::SHORT,
                false
            );
            $note = str_replace($this->availabilityDateTag, $date, $note);
        }

        return $note;
    }
}
