<?php

namespace Marketplacer\Marketplacer\Plugin;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order\Item;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use Marketplacer\BrandApi\Api\Data\OrderItemInterface;
use Marketplacer\Marketplacer\Helper\Data as MarketplacerDataHelper;
use Marketplacer\SellerApi\Api\SellerRepositoryInterface;
use Marketplacer\SellerApi\Api\MarketplacerSellerUrlInterface;
use Marketplacer\SellerApi\Api\SellerAttributeRetrieverInterface;
use ReflectionException;
use ReflectionObject;

abstract class AbstractItemOptionsPlugin
{
    /**
     * @var MarketplacerDataHelper
     */
    protected $marketplacerDataHelper;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriever;

    /**
     * @var SellerAttributeRetrieverInterface
     */
    protected $sellerAttributeRetriever;

    /**
     * @var SellerRepositoryInterface
     */
    protected $sellerRepository;

    /**
     * @var MarketplacerSellerUrlInterface
     */
    protected $sellerUrl;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * AbstractItemOptionsPlugin constructor.
     * @param MarketplacerDataHelper $marketplacerDataHelper
     * @param BrandAttributeRetrieverInterface $brandAttributeRetriever
     * @param SellerAttributeRetrieverInterface $sellerAttributeRetriever
     * @param SellerRepositoryInterface $sellerRepository
     * @param ProductResource $productResource
     * @param MarketplacerSellerUrlInterface $sellerUrl
     * @param Escaper $escaper
     */
    public function __construct(
        MarketplacerDataHelper $marketplacerDataHelper,
        BrandAttributeRetrieverInterface $brandAttributeRetriever,
        SellerAttributeRetrieverInterface $sellerAttributeRetriever,
        SellerRepositoryInterface $sellerRepository,
        ProductResource $productResource,
        \Marketplacer\SellerApi\Api\MarketplacerSellerUrlInterface $sellerUrl,
        Escaper $escaper
    ) {
        $this->marketplacerDataHelper = $marketplacerDataHelper;
        $this->brandAttributeRetriever = $brandAttributeRetriever;
        $this->sellerAttributeRetriever = $sellerAttributeRetriever;
        $this->sellerRepository = $sellerRepository;
        $this->productResource = $productResource;
        $this->sellerUrl = $sellerUrl;
        $this->escaper = $escaper;
    }

    /**
     * @param $item
     * @param $options
     * @param false $useLink
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function addOptionsToQuoteItemOptions($item, $options, $useLink = false)
    {
        $mrktOptions = [];

        $product = $item->getProduct();
        $storeId = $item->getStoreId();

        $sellerAttribute = $this->sellerAttributeRetriever->getAttribute();
        $sellerId = $product->getData($sellerAttribute->getAttributeCode());
        if ($sellerId) {
            $seller = $this->sellerRepository->getById($sellerId, $storeId);
            $sellerName = $sellerAttribute->getSource()->getOptionText($sellerId);
            if ($sellerName) {
                if ($sellerOption = $this->getSellerOption($sellerName, $sellerId, $storeId, $useLink)) {
                    $mrktOptions[] = $sellerOption;
                }
            }
            if ($this->marketplacerDataHelper->displaySellerBusinessNumber($storeId)) {
                try {
                    $sellerBusinessNumber = $seller->getBusinessNumber();
                    $sellerBusinessNumberOption = $this->getSellerBusinessNumberOption($sellerBusinessNumber, $storeId);
                    if ($sellerBusinessNumberOption) {
                        $mrktOptions[] = $sellerBusinessNumberOption;
                    }
                } catch (NoSuchEntityException $e) { //@codingStandardsIgnoreLine
                }
            }
        }

        if ($this->marketplacerDataHelper->displayBrandName($storeId)) {
            $brandAttribute = $this->brandAttributeRetriever->getAttribute();
            if ($brandId = $product->getData($brandAttribute->getAttributeCode())) {
                $brandName = $brandAttribute->getSource()->getOptionText($brandId);
                if ($brandName) {
                    if ($brandOption = $this->getBrandOption($brandName)) {
                        $mrktOptions[] = $brandOption;
                    }
                }
            }
        }

        $options = array_merge($mrktOptions, $options);

        return $options;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface|Item $item
     * @param array $options
     * @param bool $useLink
     * @return array
     */
    protected function addOptionsToOrderItemOptions($item, array $options, $useLink = false)
    {
        $mrktOptions = [];

        $storeId = $item->getStoreId();

        if ($sellerName = $item->getData(\Marketplacer\SellerApi\Api\Data\OrderItemInterface::SELLER_NAME)) {
            $sellerId = $item->getData(\Marketplacer\SellerApi\Api\Data\OrderItemInterface::SELLER_ID);
            if ($sellerId) {
                $sellerOption = $this->getSellerOption($sellerName, $sellerId, $storeId, $useLink);
                if ($sellerOption) {
                    $mrktOptions[] = $sellerOption;
                }
            }
        }

        if ($this->marketplacerDataHelper->displaySellerBusinessNumber($storeId)) {
            $sellerBusinessNumber = $item->getData(\Marketplacer\SellerApi\Api\Data\OrderItemInterface::SELLER_BUSINESS_NUMBER);
            if ($sellerBusinessNumberOption = $this->getSellerBusinessNumberOption($sellerBusinessNumber, $storeId)) {
                $mrktOptions[] = $sellerBusinessNumberOption;
            }
        }

        if ($this->marketplacerDataHelper->displayBrandName($storeId)) {
            if ($brandName = $item->getData(OrderItemInterface::BRAND_NAME)) {
                if ($brandOption = $this->getBrandOption($brandName)) {
                    $mrktOptions[] = $brandOption;
                }
            }
        }

        $options = array_merge($mrktOptions, $options);

        return $options;
    }

    /**
     * @param string $brandName
     * @return array|null
     */
    protected function getBrandOption($brandName)
    {
        if (empty($brandName)) {
            return null;
        }
        return [
            'label'       => (string)__('Brand'),
            'value'       => $brandName,
            'print_value' => $brandName,
        ];
    }

    /**
     * @param string $businessNumber
     * @param int|null $storeId
     * @return array|null
     */
    protected function getSellerBusinessNumberOption($businessNumber, $storeId = null)
    {
        if (empty($businessNumber)) {
            return null;
        }

        $title = $this->marketplacerDataHelper->getTitleForSellerBusinessNumber($storeId);
        return [
            'label'       => $title,
            'value'       => $businessNumber,
            'print_value' => $businessNumber,
        ];
    }

    /**
     * @param string $sellerName
     * @param int $sellerId
     * @param int|null $storeId
     * @param bool $useLink
     * @return array|null
     */
    protected function getSellerOption($sellerName, $sellerId, $storeId = null, $useLink = false)
    {
        if (empty($sellerName)) {
            return null;
        }
        $value = $this->getOptionVisibleValue($sellerName, $sellerId, $storeId, $useLink);
        return [
            'label'       => (string)__('Seller'),
            'value'       => $value,
            'print_value' => $sellerName,
        ];
    }

    /**
     * @param string $sellerName
     * @param int $sellerId
     * @param int|null $storeId
     * @param bool $useLink
     * @return string
     */
    protected function getOptionVisibleValue(
        $sellerName,
        $sellerId,
        $storeId = null,
        $useLink = false
    ) {
        if ($useLink && $sellerId) {
            $sellerUrl = null;
            try {
                $seller = $this->sellerRepository->getById($sellerId, $storeId);
                if ($seller && (!method_exists($seller, 'isEnabled') || $seller->isEnabled())) {
                    $sellerUrl = $this->sellerUrl->getSellerUrl($seller);
                    if ($sellerUrl) {
                        return '<a href="' . $sellerUrl . '" target="_blank">' . $sellerName . '</a>';
                    }
                }
            } catch (NoSuchEntityException $e) { //@codingStandardsIgnoreLine
            }
        }

        return $sellerName;
    }

    /**
     * @param Template $block
     * @param \Closure $proceed
     * @param array $option
     * @return array
     */
    public function aroundGetFormatedOptionValue($block, \Closure $proceed, $option)
    {
        if ($option['label'] == (string)__('Seller')) {
            return $option;
        }

        return $proceed($option);
    }

    /**
     * SORRY FOR THAT HACK :)
     *
     * @param Template $block
     * @param \Closure $proceed
     * @return string
     * @throws ReflectionException
     */
    public function aroundToHtml($block, \Closure $proceed)
    {
        $reflectionEscaper = new ReflectionObject($this->escaper);
        $reflectionPropAllowedAttributes = $reflectionEscaper->getProperty('allowedAttributes');
        try {
            $reflectionPropAllowedAttributes->setAccessible(true);
            $allowedAttributes = $prevAllowedAttributes = $reflectionPropAllowedAttributes->getValue($this->escaper);
            if (!in_array('target', $allowedAttributes)) {
                $allowedAttributes[] = 'target';
            }
            $reflectionPropAllowedAttributes->setValue($this->escaper, $allowedAttributes);
            try {
                return $proceed();
            } finally {
                $reflectionPropAllowedAttributes->setValue($this->escaper, $prevAllowedAttributes);
            }
        } finally {
            $reflectionPropAllowedAttributes->setAccessible(false);
        }
    }
}
