<?php

namespace Ewave\Navigation\Model\Menu\Type;

use Magento\Cms\Block\BlockFactory;
use Ewave\Navigation\Helper\Data;

class CmsBlock extends CustomLinkAbstract
{
    const CMS_BLOCK_CONTENT = 'cms_block_content';
    const ISL_LINK = 'is_link';

    /**
     * @var \Magento\Cms\Block\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var []
     */
    protected $blocks = [];

    /**
     * CmsBlock constructor.
     *
     * @param \Ewave\Navigation\Helper\Data $helper
     * @param \Magento\Cms\Block\BlockFactory $blockFactory
     */
    public function __construct(
        Data $helper,
        BlockFactory $blockFactory
    ) {
        parent::__construct($helper);
        $this->blockFactory = $blockFactory;
    }

    /**
     * @return array
     */
    public function getMenuData()
    {
        return [
            self::CMS_BLOCK_CONTENT => $this->getCmsBlockContent(),
            self::ISL_LINK => $this->item->getMenuItemIsLink(),
        ];
    }

    /**
     * @return string
     */
    public function getCmsBlockContent()
    {
        /**
         * @var $cmsBlock \Magento\Cms\Block\Block
         */
        $cmsBlock = $this->blockFactory->create();

        if (!isset($this->blocks[$this->item->getCmsBlockId()])) {
            $this->blocks[$this->item->getCmsBlockId()] = ($cmsBlock->setBlockId($this->item->getCmsBlockId())
                ->toHtml());
        }

        return $this->blocks[$this->item->getCmsBlockId()] ?? '';
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return parent::isAvailable() && $this->getCmsBlockContent() !== '';
    }
}
