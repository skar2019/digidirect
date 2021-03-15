<?php
namespace Digidirect\ProductMultiView\Block\Product;

use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Digidirect\ProductMultiView\Helper\Data as MultiViewHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\Image as ImageBlock;
use Magento\Framework\App\ObjectManager;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    /**
     * @var \Digidirect\ProductMultiView\Helper\Data
     */
    protected $_helper;

    /**
     * @param HelperFactory $helperFactory
     * @param ImageFactory $imageFactory
     * @param MultiViewHelper $helper
     */
    public function __construct(
        HelperFactory $helperFactory,
        ImageFactory $imageFactory,
        MultiViewHelper $helper
    ) {
        parent::__construct($helperFactory, $imageFactory);
        $this->_helper = $helper;
    }

    /**
     * Create image block
     *
     * @param Product|null $product
     * @param string|null $imageId
     * @param array|null $attributes
     * @return ImageBlock
     */
    public function create(Product $product = null, string $imageId = null, array $attributes = null)
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()
            ->init($product ?: $this->product, $imageId ?: $this->imageId);

        $template = $helper->getFrame()
            ? 'Digidirect_ProductMultiView::product/image.phtml'
            : 'Digidirect_ProductMultiView::product/image_with_borders.phtml';

        $imageSize = $helper->getResizedImageInfo();

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $helper->getUrl(),
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' =>  $this->getRatio($helper),
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => !empty($imageSize[0]) ? $imageSize[0] : $helper->getWidth(),
                'resized_image_height' => !empty($imageSize[1]) ? $imageSize[1] : $helper->getHeight(),
                'product' => $product
            ],
        ];

        $productHoverImage = $this->_helper->getProductHoverImage($product ?: $this->product);
        if ($productHoverImage) {
            $multiViewHelper = $this->helperFactory->create()
                ->init($product ?: $this->product, $imageId ?: $this->imageId);

            $multiViewHelper->setImageFile($productHoverImage);
            $multiViewImageSize = $multiViewHelper->getResizedImageInfo();

            $data['data']['multi_view_image_url'] = $multiViewHelper->getUrl();
            $data['data']['multi_view_resized_image_width'] =
                !empty($multiViewImageSize[0]) ? $multiViewImageSize[0] : $multiViewHelper->getWidth();
            $data['data']['multi_view_resized_image_height'] =
                !empty($multiViewImageSize[1]) ? $multiViewImageSize[1] : $multiViewHelper->getHeight();
        }

        return ObjectManager::getInstance()->create(ImageBlock::class, $data);
    }
}
