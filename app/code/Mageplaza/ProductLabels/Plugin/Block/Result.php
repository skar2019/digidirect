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

use Magento\Framework\Exception\LocalizedException;
use Mageplaza\ProductLabels\Helper\Data as HelperData;

/**
 * Class Result
 * @package Mageplaza\ProductLabels\Plugin\Block
 */
class Result
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * View constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->_helperData = $helperData;
    }

    /**
     * @param \Magento\CatalogSearch\Block\Result $result
     * @param                                     $html
     *
     * @return string
     * @throws LocalizedException
     */
    public function afterGetProductListHtml(\Magento\CatalogSearch\Block\Result $result, $html)
    {
        if ($this->_helperData->isEnabled() && $result->getRequest()->isAjax()) {
            $html .= $result->getLayout()->getBlock('mp.productlabels.listing.label')->toHtml();
        }

        return $html;
    }
}
