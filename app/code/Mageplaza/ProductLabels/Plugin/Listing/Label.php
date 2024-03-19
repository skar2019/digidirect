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

namespace Mageplaza\ProductLabels\Plugin\Listing;

use Closure;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Mageplaza\ProductLabels\Helper\Data as HelperData;

/**
 * Class Label
 * @package Mageplaza\ProductLabels\Plugin\Listing
 */
class Label
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var int
     */
    protected $temp = 0;

    /**
     * Label constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->_helperData = $helperData;
    }

    /**
     * @param ListProduct $subject
     * @param Closure $proceed
     * @param $product
     *
     * @return mixed|string
     * @throws LocalizedException
     */
    public function aroundGetProductPrice(ListProduct $subject, Closure $proceed, $product)
    {
        $isEnabled     = $this->_helperData->isEnabled();
        $isAjaxRequest = $subject->getRequest()->isAjax() && $isEnabled;
        $result        = $proceed($product);

        if ($isEnabled || $isAjaxRequest) {
            $this->temp++;
            $result .= $subject->getLayout()
                ->createBlock(\Mageplaza\ProductLabels\Block\Listing\Label::class)
                ->setTemplate('Mageplaza_ProductLabels::listing/view/label.phtml')
                ->setCatProduct($product)
                ->toHtml();

            /** compatible infinite scroll Layered */
            if ($isAjaxRequest && $this->temp === 1) {
                $result .= $subject->getLayout()
                    ->createBlock(Template::class)
                    ->setTemplate('Mageplaza_ProductLabels::listing/view/listing.phtml')
                    ->toHtml();
            }
        }

        return $result;
    }
}
