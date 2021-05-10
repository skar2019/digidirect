<?php

namespace Digidirect\PreOrder\Plugin;

/**
 * Class ProductsList
 * @package Digidirect\PreOrder\Plugin
 */
class ProductsList
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
     * @param \Magento\CatalogWidget\Block\Product\ProductsList|\Magento\Catalog\Block\Product\AbstractProduct $subject
     * @param \Closure $closure
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function aroundGetProductPriceHtml(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        \Closure $closure,
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = 'item_list',
        $arguments = []
    ) {
        $htmlPreorder = '';
        if (!$subject instanceof \Magento\GroupedProduct\Block\Product\View\Type\Grouped
            && $this->preOrderHelper->getConfig()->preordersEnabled()
            && $this->preOrderHelper->isProductPreorder($product)
        ) {
            $htmlPreorder = $subject->getLayout()
                ->createBlock('Digidirect\PreOrder\Block\Product\ListProduct\Preorder')
                ->setProduct($product)
                ->setTemplate('product/list/preorder.phtml')
                ->toHtml();
        }

        return $htmlPreorder . $closure($product, $priceType, $renderZone, $arguments);
    }
}
