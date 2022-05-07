<?php

namespace Marketplacer\Marketplacer\Plugin\Sales\Block;

use Closure;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order\Item;
use Marketplacer\Marketplacer\Helper\Data as MarketplacerDataHelper;
use Marketplacer\SellerApi\Api\Data\OrderItemInterface;

abstract class AbstractItemOptionsPlugin
{
    /**
     * @var MarketplacerDataHelper
     */
    protected $marketplacerDataHelper;

    /**
     * AbstractItemOptionsPlugin constructor.
     * @param MarketplacerDataHelper $marketplacerDataHelper
     */
    public function __construct(
        MarketplacerDataHelper $marketplacerDataHelper
    ) {
        $this->marketplacerDataHelper = $marketplacerDataHelper;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface|Item $item
     * @param array $options
     * @param bool $useLink
     * @return array
     */
    protected function addOptions($item, array $options)
    {
        $mrktOptions = [];
        $storeId = $item->getStoreId();
        if ($this->marketplacerDataHelper->displayBrandName($storeId)) {
            $brandName = $item->getData(\Marketplacer\BrandApi\Api\Data\OrderItemInterface::BRAND_NAME);
            if ($brandName) {
                $mrktOptions[] = [
                    'label'       => (string)__('Brand'),
                    'value'       => $brandName,
                    'print_value' => $brandName,
                ];
            }
        }

        if ($sellerName = $item->getData(OrderItemInterface::SELLER_NAME)) {
            $mrktOptions[] = [
                'label'       => (string)__('Seller'),
                'value'       => $sellerName,
                'print_value' => $sellerName,
            ];
        }

        if ($this->marketplacerDataHelper->displaySellerBusinessNumber($storeId)) {
            $sellerABN = $item->getData(OrderItemInterface::SELLER_BUSINESS_NUMBER);
            if ($sellerABN) {
                $title = $this->marketplacerDataHelper->getTitleForSellerBusinessNumber($storeId);
                $mrktOptions[] = [
                    'label'       => $title,
                    'value'       => $sellerABN,
                    'print_value' => $sellerABN,
                ];
            }
        }

        $options = array_merge($mrktOptions, $options);

        return $options;
    }

    /**
     * @param Template $block
     * @param Closure $proceed
     * @param array $option
     * @return array
     */
    public function aroundGetFormatedOptionValue($block, Closure $proceed, $option)
    {
        if ($option['label'] == (string)__('Seller')) {
            return $option;
        }

        return $proceed($option);
    }
}
