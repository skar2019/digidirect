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

namespace Mageplaza\ProductLabels\Block\Listing;

use Exception;
use Magento\Catalog\Model\Product;
use Mageplaza\ProductLabels\Block\Label as AbstractLabel;
use Mageplaza\ProductLabels\Helper\Data;

/**
 * Class Label
 * @package Mageplaza\ProductLabels\Block\Listing
 */
class Label extends AbstractLabel
{
    /**
     * @param Product $product
     *
     * @return array
     */
    public function getDataLabels(Product $product)
    {
        if (!$product->isSaleable() || (int) $product->getIsInStock() === 0) {
            if ($this->_helperData->checkStockStatus($product)) {
                $ruleId    = $this->_helperData->getOutOfStockLabel();
                $ruleData  = $this->metaFactory->create()->getRuleFromRuleId($ruleId);
                $labelData = [];

                if (!empty($ruleData)) {
                    foreach ($ruleData as $data) {
                        if ($data['product_id'] === $product->getId()) {
                            $labelData[] = $data;
                        }
                    }
                }

                return $labelData;
            }
        }

        return $this->metaFactory->create()->getRulesFromProduct($product);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->getCatProduct();
    }

    /**
     * @param array $data
     *
     * @return bool|string
     */
    public function getImgSrc($data)
    {
        if ($data['template_url']) {
            return $this->getTemplateUrl($data['template_url']);
        }

        if ($data['img_url']) {
            return $data['img_url'];
        }

        return false;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function getLabelPosition($data)
    {
        $fontSize = $data['label_fontsize'];
        $posData  = Data::jsonDecode($data['label_position']);
        $width    = $posData['label']['width'] * 100 / $this->getProductImgWidth();
        $height   = $posData['label']['height'] * 100 / $this->getProductImgHeight();
        $top      = (($this->getProductImgHeight() - $posData['label']['height']) *
                $posData['label']['percentTop'] / 100) / $this->getProductImgHeight() * 100;
        $left     = (($this->getProductImgWidth() - $posData['label']['width']) *
                $posData['label']['percentLeft'] / 100) / $this->getProductImgWidth() * 100;

        return sprintf(
            'width: %s%%; height: %s%%; top: %s%%; left: %s%%; font-size: %spx',
            $width,
            $height,
            $top,
            $left,
            $fontSize
        );
    }

    /**
     * @return string
     */
    public function getProductImgWidth()
    {
        return $this->_gallery->getImageAttribute('category_page_list', 'width', 1);
    }

    /**
     * @return string
     */
    public function getProductImgHeight()
    {
        return $this->_gallery->getImageAttribute('category_page_list', 'height', 1);
    }

    /**
     * @param array $data
     *
     * @return mixed|string
     */
    public function getProductTooltip($data)
    {
        $ruleId             = $data['rule_id'];
        $productTooltip     = Data::jsonDecode($data['product_tooltip']);
        $listProductTooltip = Data::jsonDecode($data['list_product_tooltip']);
        $rule               = $this->_ruleFactory->create()->load($ruleId);
        $storeCode          = $this->_helperData->getStore()->getCode();

        if ($rule->getSame()) {
            $productTooltip = $this->escapeHtml(isset($productTooltip[$storeCode])
                && $productTooltip[$storeCode] ?
                    $productTooltip[$storeCode] : ($productTooltip['admin'] ?: ''));
        } else {
            $productTooltip = $this->escapeHtml(isset($listProductTooltip[$storeCode])
                && $listProductTooltip[$storeCode] ?
                    $listProductTooltip[$storeCode] : ($listProductTooltip['admin'] ?: ''));
        }

        return $productTooltip;
    }

    /**
     * @param array $dataLabels
     *
     * @return array
     */
    public function prepareDataLabels($dataLabels)
    {
        $storeId       = $this->getStoreId();
        $newDataLabels = [];
        if ($dataLabels) {
            $ruleIds = array_unique(array_column($dataLabels, 'rule_id'));
            foreach ($dataLabels as $key => $dataLabel) {
                if (in_array($dataLabel['rule_id'], $ruleIds, true)) {
                    if ((int) $storeId === (int) $dataLabel['store_id']) {
                        $key                 = array_search(
                            $dataLabel['rule_id'],
                            array_column($dataLabels, 'rule_id')
                        );
                        $newDataLabels[$key] = $dataLabel;
                    } else {
                        $newRuleIds = array_unique(array_column($newDataLabels, 'rule_id'));
                        if (!in_array($dataLabel['rule_id'], $newRuleIds, true)) {
                            $newDataLabels[$key] = $dataLabel;
                        }
                    }
                }
            }

            return $newDataLabels;
        }

        return $dataLabels;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        try {
            $storeId = $this->_helperData->getStore()->getId();
        } catch (Exception $e) {
            $storeId = 0;
        }

        return $storeId;
    }
}
