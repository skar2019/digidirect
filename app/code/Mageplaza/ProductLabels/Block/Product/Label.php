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

namespace Mageplaza\ProductLabels\Block\Product;

use Mageplaza\ProductLabels\Block\Label as AbstractLabel;
use Mageplaza\ProductLabels\Helper\Data as HelperData;
use Mageplaza\ProductLabels\Model\Rule;

/**
 * Class Label
 * @package Mageplaza\ProductLabels\Block\Product
 */
class Label extends AbstractLabel
{
    const GALLERY_WIDTH  = 337;
    const GALLERY_HEIGHT = 337;

    /**
     * param label
     *
     * @param $rule
     *
     * @return string
     */
    public function getDataLabel($rule)
    {
        $posData = HelperData::jsonDecode($rule->getLabelPosition());

        $params = [
            'ruleId'           => $rule->getId(),
            'productId'        => $this->getProduct()->getId(),
            'labelFont'        => $rule->getLabelFont(),
            'labelFontSize'    => $rule->getLabelFontSize(),
            'labelColor'       => $rule->getLabelColor(),
            'labelTopPercent'  => $posData['label']['percentTop'],
            'labelLeftPercent' => $posData['label']['percentLeft'],
            'labelWidth'       => $posData['label']['width'],
            'labelHeight'      => $posData['label']['height'],
            'galleryWidth'     => $this->getGalleryWidth(),
            'galleryHeight'    => $this->getGalleryHeight(),
        ];

        return HelperData::jsonEncode($params);
    }

    /**
     * Get Src image label
     *
     * @param Rule $rule
     *
     * @return string
     */
    public function getSrcImg($rule)
    {
        if ($label = $rule->getLabelTemplate()) {
            return $this->getTemplateUrl($label);
        }
        if ($labelImage = $rule->getLabelImage()) {
            return $this->_helperData->getImageUrl($labelImage, 'product');
        }

        return null;
    }

    /**
     * Replace id custom css
     *
     * @param $rule
     *
     * @return mixed
     */
    public function replaceCustomCss($rule)
    {
        $customCss = $rule->getLabelCss();
        $productId = $this->getProduct()->getId();

        $search  = [
            'design-labels',
            'design-label-image',
            'design-label-text',
        ];
        $replace = [
            'design-labels-' . $rule->getId() . '-' . $productId,
            'design-label-image-' . $rule->getId() . '-' . $productId,
            'design-label-text-' . $rule->getId() . '-' . $productId
        ];

        return str_replace($search, $replace, $customCss);
    }

    /**
     * Get width image product
     *
     * @return string
     */
    public function getGalleryWidth()
    {
        if ($this->isPortoTheme()) {
            return self::GALLERY_WIDTH;
        }

        return $this->_gallery->getImageAttribute('product_page_image_medium', 'width');
    }

    /**
     * Get height image product
     *
     * @return string
     */

    /**
     * @return int|string
     */
    public function getGalleryHeight()
    {
        if ($this->isPortoTheme()) {
            return self::GALLERY_HEIGHT;
        }

        return $this->_gallery->getImageAttribute('product_page_image_medium', 'height');
    }
}
