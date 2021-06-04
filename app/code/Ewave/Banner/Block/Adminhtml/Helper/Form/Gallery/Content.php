<?php

namespace Ewave\Banner\Block\Adminhtml\Helper\Form\Gallery;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Banner\Model\Banner as BannerModel;

class Content extends AbstractElement
{
    /**
     * Layout
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var string
     */
    protected $contentBlockToCreate;

    /**
     * @var string
     */
    protected $blockToCreate;

    /**
     * Content constructor.
     *
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param string $contentBlockToCreate
     * @param string $blockToCreate
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\LayoutInterface $layout,
        $contentBlockToCreate = \Ewave\Banner\Block\Adminhtml\Banner\Helper\Form\Gallery\Content::class,
        $blockToCreate = \Ewave\Banner\Block\Adminhtml\Banner\Edit\Tab\Images\Video::class,
        $data = []
    ) {
        $this->contentBlockToCreate = $contentBlockToCreate;
        $this->blockToCreate = $blockToCreate;
        $this->layout = $layout;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * Get html
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->getContentHtml();
    }

    /**
     * Get html images gallery
     *
     * @return string
     */
    public function getContentHtml()
    {
        /**
         * @var $content \Ewave\Banner\Block\Adminhtml\Banner\Helper\Form\Gallery\Content
         */
        $content = $this->layout->createBlock($this->contentBlockToCreate);
        $block = $this->layout->createBlock($this->blockToCreate, 'new-video');

        $content->setChild('new-video', $block);
        $content->setId($this->getHtmlId() . '_content')->setElement($this);
        $galleryJs = $content->getJsObjectName();
        $content->getUploader()->getConfig()->setMegiaGallery($galleryJs);
        return $content->toHtml();
    }

    /**
     * Banner Model
     *
     * @return BannerModel
     */
    public function getDataObject()
    {
        return $this->getForm()->getDataObject();
    }
}
