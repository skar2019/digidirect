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
}
