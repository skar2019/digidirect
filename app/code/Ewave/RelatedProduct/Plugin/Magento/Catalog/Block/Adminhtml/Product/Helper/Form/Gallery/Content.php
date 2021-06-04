<?php
namespace Ewave\RelatedProduct\Plugin\Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery;

use Ewave\RelatedProduct\Block\Adminhtml\Product\Helper\Form\Gallery\Content as GalleryContent;

/**
 * Class Content
 * @author Ewave team
 * @package Ewave\RelatedProduct\Block\Adminhtml\Product\Helper\Form\Gallery
 */
class Content
{
    /**
     * @param GalleryContent $galleryBlock
     * @return void
     */
    public function beforeToHtml(GalleryContent $galleryBlock)
    {
        if ($galleryBlock->canShow()) {
            $galleryBlock->setTemplate('Ewave_RelatedProduct::helper/gallery.phtml');
        }
    }
}
