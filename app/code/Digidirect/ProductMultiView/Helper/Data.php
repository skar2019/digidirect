<?php
namespace Digidirect\ProductMultiView\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const CONFIG_ENABLED = 'digidirect_productmultiview/product_multi_view/enabled';

    const CONFIG_DISPLAY_RANDOM_HOVER = 'digidirect_productmultiview/product_multi_view/display_random_hover';

    const ALTERNATIVE_IMAGE_KEY = 'alternative_image';

    /**
     * @var GalleryReadHandler
     */
    protected $_galleryReadHandler;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param GalleryReadHandler $galleryReadHandler
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        GalleryReadHandler $galleryReadHandler
    ) {
        parent::__construct($context);
        $this->_galleryReadHandler = $galleryReadHandler;
    }

    /**
     * @return bool
     */
    public function isMultiViewEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_ENABLED,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return bool
     */
    public function isDisplayRandomHover()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_DISPLAY_RANDOM_HOVER,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @param Product $product
     * @return bool|string
     */
    public function getProductHoverImage(Product $product)
    {
        if (!$this->isMultiViewEnabled()) {
            return false;
        }

        $this->_galleryReadHandler->execute($product);
        $alternativeImage = $product->getData(self::ALTERNATIVE_IMAGE_KEY);
        if ($alternativeImage && $alternativeImage != 'no_selection') {
            return $alternativeImage;
        }

        if ($this->isDisplayRandomHover()) {
            $productImages = $product->getMediaGalleryImages();
            foreach ($productImages as $image) {
                if ($image->getFile() != $product->getSmallImage()) {
                    return $image->getFile();
                }
            }
        }

        return false;
    }
}
