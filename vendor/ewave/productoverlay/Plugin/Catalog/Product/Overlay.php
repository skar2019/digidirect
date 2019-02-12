<?php

namespace Ewave\ProductOverlay\Plugin\Catalog\Product;

/**
 * Class Overlay
 *
 * @package Ewave\ProductOverlay\Plugin\Catalog\Product
 */
class Overlay
{
    /**
     * @var \Ewave\ProductOverlay\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var array
     */
    protected $data;

    /**
     * Overlay constructor.
     *
     * @param \Ewave\ProductOverlay\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $data
     */
    public function __construct(
        \Ewave\ProductOverlay\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_request = $request;
        $this->data = $data;
    }

    /**
     * @param \Magento\Catalog\Block\Product\Image $subject
     * @param mixed $result
     * @return string
     */
    public function afterToHtml(
        \Magento\Catalog\Block\Product\Image $subject,
        $result
    ) {
        $product = $subject->getProduct();
        if ($product && $this->_request->getFullActionName() == 'catalog_category_view'
            || $this->needToProcessOverlay($subject)
        ) {
            $result .= $this->_helper->renderProductOverlay($product, 'category');
        }
        return $result;
    }

    /**
     * @param \Magento\Catalog\Block\Product\Image $subject
     * @return bool
     */
    protected function needToProcessOverlay($subject)
    {
        if (!$this->processHandle()) {
            return false;
        }
        $instances = $this->data['instances'] ?? [];
        foreach ($instances as $instance) {
            if ($subject instanceof $instance) {
                return false;
            }
        }
        return true;
    }

    /**
     * Exclude processing overlays on wishlist page - https://ewave.tpondemand.com/entity/161341
     *
     * @return bool
     */
    protected function processHandle()
    {
        $handle = trim($this->getHandle());
        $excludeHandles = $this->data['exclude_handles'] ?? [];
        foreach ($excludeHandles as $excludeHandle) {
            if (trim($excludeHandle) == $handle) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getHandle()
    {
        return $this->_request->getFullActionName();
    }
}
