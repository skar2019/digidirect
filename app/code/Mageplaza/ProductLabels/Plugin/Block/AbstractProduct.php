<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Plugin\Block;

use Exception;
use Magento\Catalog\Block\Product\AbstractProduct as CatalogAbstractProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Mageplaza\ProductLabels\Block\Listing\Label;
use Mageplaza\ProductLabels\Helper\Data;

/**
 * Class AbstractProduct
 * @package Mageplaza\ProductLabels\Plugin\Block
 */
class AbstractProduct
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var int
     */
    protected $temp = 0;

    /**
     * AbstractProduct constructor.
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param CatalogAbstractProduct $abstractProduct
     * @param callable $proceed
     * @param Product $product
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundGetProductPrice(CatalogAbstractProduct $abstractProduct, callable $proceed, Product $product)
    {
        $result = $proceed($product);

        try {
            $storeId = $this->helperData->getStore()->getId();
        } catch (Exception $e) {
            $storeId = null;
        }
        if ($this->helperData->isEnabled($storeId)) {
            $this->temp++;
            if ($this->temp === 1) {
                $result .= $abstractProduct->getLayout()
                    ->createBlock(Template::class)
                    ->setIsShowLabels(true)
                    ->setTemplate('Mageplaza_ProductLabels::listing/view/listing.phtml')
                    ->toHtml();
            }

            switch ($abstractProduct->getType()) {
                case 'related-rule':
                case 'related':
                    if ($this->helperData->isShowLabelsRelatedProducts()) {
                        $result .= $this->getProductLabelHtml($abstractProduct, $product);
                    }
                    break;
                case 'upsell-rule':
                case 'upsell':
                    if ($this->helperData->isShowLabelsUpsellProducts()) {
                        $result .= $this->getProductLabelHtml($abstractProduct, $product);
                    }
                    break;
                case 'crosssell-rule':
                case 'crosssell':
                    if ($this->helperData->isShowLabelsCrossSellProducts()) {
                        $result .= $this->getProductLabelHtml($abstractProduct, $product, true);
                    }
                    break;
            }
        }

        return $result;
    }

    /**
     * @param CatalogAbstractProduct $abstractProduct
     * @param Product $product
     * @param bool $isCrossSell
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getProductLabelHtml($abstractProduct, $product, $isCrossSell = false)
    {
        return $abstractProduct->getLayout()->createBlock(Label::class)
            ->setTemplate('Mageplaza_ProductLabels::listing/view/label.phtml')
            ->setIsShowLabels(true)
            ->setIsCrossSell($isCrossSell)
            ->setCatProduct($product)
            ->toHtml();
    }
}
