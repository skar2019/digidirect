<?php
namespace Digidirect\ProductMultiView\Plugin\Catalog\Ui\DataProvider\Product\Listing\Collector;

use Digidirect\ProductMultiView\Helper\Data as HelperData;
use Magento\Catalog\Api\Data\ProductRender\ImageInterfaceFactory;
use Magento\Catalog\Ui\DataProvider\Product\Listing\Collector\Image as Subject;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Api\Data\ProductRenderInterface;

class Image
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
     * @var ImageInterfaceFactory
     */
    protected $_imageRenderInfoFactory;

    /**
     * Image constructor.
     * @param HelperData $helper
     * @param ImageFactory $imageFactory
     * @param ImageInterfaceFactory $imageInterfaceFactory
     */
    public function __construct(
        HelperData $helper,
        ImageFactory $imageFactory,
        ImageInterfaceFactory $imageInterfaceFactory
    ) {
        $this->_helper = $helper;
        $this->_imageFactory = $imageFactory;
        $this->_imageRenderInfoFactory = $imageInterfaceFactory;
    }

    /**
     * @param Subject $subject
     * @param $result
     * @param ModelProduct $product
     * @param ProductRenderInterface $productRender
     * @return mixed
     */
    public function afterCollect(
        Subject $subject,
        $result,
        ModelProduct $product,
        ProductRenderInterface $productRender
    ) {
        $hoverImage = $this->_helper->getProductHoverImage($product);
        if ($hoverImage !== false) {
            $images = $productRender->getImages();
            $imageHelper = $this->_imageFactory->create()
                ->init($product, 'recently_viewed_widget_alternative_image');

            $imageHelper->setImageFile($hoverImage);

            $alternativeImage = $this->_imageRenderInfoFactory->create();
            $alternativeImage->setUrl($imageHelper->getUrl());
            $alternativeImage->setCode(HelperData::ALTERNATIVE_IMAGE_KEY);
            $alternativeImage->setHeight($imageHelper->getHeight());
            $alternativeImage->setWidth($imageHelper->getWidth());
            $alternativeImage->setLabel($imageHelper->getLabel());

            try {
                $reSizedInfo = $imageHelper->getResizedImageInfo();
            } catch (ModelProduct\Image\NotLoadInfoImageException $exception) {
                $reSizedInfo = [$imageHelper->getWidth(), $imageHelper->getHeight()];
            }

            $alternativeImage->setResizedHeight($reSizedInfo[1]);
            $alternativeImage->setResizedWidth($reSizedInfo[0]);

            $images[] = $alternativeImage;
            $productRender->setImages($images);
        }
        return $result;
    }
}
