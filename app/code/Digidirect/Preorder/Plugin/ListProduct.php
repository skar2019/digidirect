<?php

namespace Digidirect\PreOrder\Plugin;

/**
 * Class ListProduct
 *
 * @package Digidirect\PreOrder\Plugin
 */
class ListProduct
{
    /**
     * @var \Digidirect\PreOrder\Helper\Data
     */
    private $preOrderHelper;

    /**
     * ListProduct constructor.
     *
     * @param \Digidirect\PreOrder\Helper\Data $preOrderHelper
     */
    public function __construct(
        \Digidirect\PreOrder\Helper\Data $preOrderHelper
    ) {
        $this->preOrderHelper = $preOrderHelper;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param \Closure $closure
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function aroundGetReviewsSummaryHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $closure,
        \Magento\Catalog\Model\Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        $htmlPreorder = '';
        if ($this->preOrderHelper->getConfig()->preordersEnabled()
            && $this->preOrderHelper->isProductPreorder($product)
        ) {
            $htmlPreorder = $subject->getLayout()
                ->createBlock('Digidirect\PreOrder\Block\Product\ListProduct\Preorder')
                ->setProduct($product)
                ->setTemplate('product/list/preorder.phtml')
                ->toHtml();
        }

        return $htmlPreorder . $closure($product, $templateType, $displayIfNoReviews);
    }
}
