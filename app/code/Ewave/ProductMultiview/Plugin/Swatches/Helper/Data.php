<?php
namespace Ewave\ProductMultiView\Plugin\Swatches\Helper;

use Ewave\ProductMultiView\Helper\Data as HelperData;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Helper\ImageFactory;

class Data
{
    /**
     * @var HelperData
     */
    protected $_helper;

    /**
     * @var ImageFactory
     */
    protected $_imageFactory;

    /**
     * Image constructor.
     * @param HelperData $helper
     * @param ImageFactory $imageFactory
     */
    public function __construct(
        HelperData $helper,
        ImageFactory $imageFactory
    ) {
        $this->_helper = $helper;
        $this->_imageFactory = $imageFactory;
    }

    /**
     * @param \Magento\Swatches\Helper\Data $swatcheshelper
     * @param \Closure $proceed
     * @param ModelProduct $product
     * @return []
     */
    public function aroundGetProductMediaGallery(
        \Magento\Swatches\Helper\Data $swatcheshelper,
        \Closure $proceed,
        ModelProduct $product
    ) {
        $result = $proceed($product);
        $result[HelperData::ALTERNATIVE_IMAGE_KEY] = '';
        $hoverImage = $this->_helper->getProductHoverImage($product);
        if ($hoverImage !== false) {
            $imageHelper = $this->_imageFactory->create()
                ->init($product, 'small_image');

            $imageHelper->setImageFile($hoverImage);
            $result[HelperData::ALTERNATIVE_IMAGE_KEY] = $imageHelper->getUrl();
        }
        return $result;
    }
}
