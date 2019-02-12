<?php

namespace Ewave\ProductOverlay\Plugin\Catalog\Product;

use Ewave\ProductOverlay\Helper\Data;
use Magento\Catalog\Model\Product;

/**
 * Class ImageBuilder
 * @package Ewave\ProductOverlay\Plugin\Catalog\Product
 */
class ImageBuilder
{
    /**
     * @var \Ewave\ProductOverlay\Helper\Data
     */
    protected $_helper;

    /**
     * ImageBuilder constructor.
     * @param \Ewave\ProductOverlay\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Data $helper,
        \Magento\Framework\Registry $registry
    ) {
        $this->_helper  = $helper;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ImageBuilder $subject
     * @param mixed $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreate(
        \Magento\Catalog\Block\Product\ImageBuilder $subject,
        $result
    ) {
        $product = $this->registry->registry(Data::CURRENT_PRODUCT_REGISTRY);
        if ($product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            $result->setProduct($product);
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ImageBuilder $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSetProduct(
        \Magento\Catalog\Block\Product\ImageBuilder $subject,
        \Closure $proceed,
        Product $product
    ) {
        $result = $proceed($product);
        $this->registry->unregister(Data::CURRENT_PRODUCT_REGISTRY);
        $this->registry->register(Data::CURRENT_PRODUCT_REGISTRY, $product);

        return $result;
    }
}
