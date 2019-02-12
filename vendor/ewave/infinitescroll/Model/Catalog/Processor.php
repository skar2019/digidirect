<?php

namespace Ewave\InfiniteScroll\Model\Catalog;

use Ewave\InfiniteScroll\Helper\Data as InfiniteScrollHelper;
use Ewave\InfiniteScroll\Model\ProcessorInterface;
use Magento\Catalog\Block\Product\ListProduct;
use \Magento\Framework\View\LayoutInterface;

class Processor implements ProcessorInterface
{
    const BLOCK_NAME = 'category.products.list';
    const ENCODING = 'UTF-8';
    
    /**
     * @var string
     */
    protected $_selector;

    /**
     * @var InfiniteScrollHelper
     */
    protected $_helper;

    /**
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * Processor constructor.
     * @param InfiniteScrollHelper $helper
     * @param LayoutInterface $layout
     * @param array $data
     */
    public function __construct(InfiniteScrollHelper $helper, LayoutInterface $layout, $data)
    {
        $this->_selector = $data['selector'];
        $this->_helper = $helper;
        $this->_layout = $layout;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function process()
    {
        /** @var ListProduct $block */
        $block = $this->_getBlock();
        $url = false;
        $resultHtml = '';
        $totalCount = 0;
        $currentCount = 0;
        $perPage = 0;
        if ($block->getLoadedProductCollection()->getSize()) {
            $html = $block->toHtml();

            $totalCount = $this->getTotalSize();
            $perPage = $this->getLimit();
            $currentCount = $this->getCurrentSize();

            $dom = new \Zend_Dom_Query();
            $dom->setDocumentHtml(mb_convert_encoding($html, 'HTML-ENTITIES', static::ENCODING));
            $result = $dom->query($this->_selector);
            $resultHtml = '';
            if ($result->count()) {
                foreach ($result as $match) {
                    /** @var \DOMNode $node */
                    foreach ($match->childNodes as $node) {
                        if (trim($node->nodeValue)) {
                            $resultHtml .= $node->C14N();
                        }
                    }
                }
            }
            $url = $this->_getNextPageUrl();
        }

        return [
            'url' => $url,
            'content' => $resultHtml,
            'totalCount' => $totalCount,
            'currentCount' => $currentCount,
            'perPageCount' => $perPage
        ];
    }

    /**
     * @return bool|string
     * @throws \Exception
     */
    protected function _getNextPageUrl()
    {
        /** @var ListProduct $block */
        $block = $this->_getBlock();
        /** @var \Ewave\InfiniteScroll\Block\Product\ProductList\Toolbar $toolbar */
        $toolbar = $block->getToolbarBlock();
        $pager = $toolbar->getPager();

        $url = false;
        if ($pager && !$pager->isLastPage()) {
            $url = htmlspecialchars_decode($pager->getNextPageUrl());
        }
        return $url;
    }

    /**
     * @return ListProduct
     * @throws \Exception
     */
    protected function _getBlock()
    {
        $block = $this->_layout->getBlock(static::BLOCK_NAME);
        if (!$block) {
            throw new \Exception('block not found');
        }
        $block->setToolbarBlockName('infinitescroll.toolbar');
        return $block;
    }

    /**
     * @return bool|string
     */
    public function getNextPageUrl()
    {
        return $this->_getNextPageUrl();
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_helper->isCatalogProcessorEnabled();
    }

    /**
     * @return int
     */
    public function getActionType()
    {
        return $this->_helper->getCatalogActionType();
    }

    /**
     * @return int
     */
    protected function _getLimit()
    {
        return $this->_helper->getCatalogLimit();
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->_getLimit();
    }

    /**
     * @return int
     */
    public function getTotalSize()
    {
        $block = $this->_getBlock();
        return $block->getLoadedProductCollection()->getSize();
    }

    /**
     * @return int
     */
    public function getCurrentSize()
    {
        /** @var ListProduct $block */
        $block = $this->_getBlock();
        $collection = $block->getLoadedProductCollection();
        return $collection->count() + ($this->getLimit() * ($collection->getCurPage() - 1));
    }
}
