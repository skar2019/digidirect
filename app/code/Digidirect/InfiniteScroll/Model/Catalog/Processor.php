<?php

namespace Digidirect\InfiniteScroll\Model\Catalog;

use Digidirect\InfiniteScroll\Helper\Data as InfiniteScrollHelper;
use Digidirect\InfiniteScroll\Model\ProcessorInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product\ProductList\Toolbar as CatalogToolbar;
use Magento\Framework\View\LayoutInterface;

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
            $totalCount = $this->getTotalSize();
            $perPage = $this->getLimit();
            
            $pagerData = $this->_getNextPageUrl();
            
            $currentCount = $this->getCurrentSize();
            
            $initialCurrentPage = 1;
            
            if(isset($_GET["p"])){
                $initialCurrentPage = $_GET["p"];
                settype($initialCurrentPage, "integer");
            }
            
            $toolbar = $block->getToolbarBlock();
            $toolbar->nextPage();
            
            $html = $block->toHtml();

//            $dom = new \Zend_Dom_Query();
//            $dom->setDocumentHtml(mb_convert_encoding($html, 'HTML-ENTITIES', static::ENCODING));
//            $result = $dom->query($this->_selector);
//            if ($result->count()) {
//                foreach ($result as $match) {
//                    /** @var \DOMNode $node */
//                    foreach ($match->childNodes as $node) {
//                        if (trim($node->nodeValue)) {
//                            $resultHtml .= $node->ownerDocument->saveHTML($node);
//                        }
//                    }
//                }
//            }
            
            $dom = new \Zend_Dom_Query($html);
            $results = $dom->query(".product-items");

            $count = count($results); // get number of matches: 4
            $resultHtml = "";
            $resultNode = "";
            
            if($count > 0){
                foreach ($results as $result) {
                    $resultNode .= $result->nodeValue;
                    $resultHtml .= $result->C14N();
                }
            }
            
        }

        return [
            'url' => $pagerData["url"],
            'content' => $resultHtml,
            'resultNode' => $resultNode,
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
        $data = array();
        
        $block = $this->_getBlock();
        /** @var \Digidirect\InfiniteScroll\Block\Product\ProductList\Toolbar $toolbar */
        $toolbar = $block->getToolbarBlock();
        $pager = $toolbar->getPager();

        $url = false;
        if ($pager && !$pager->isLastPage()) {
            $url = htmlspecialchars_decode($pager->getNextPageUrl());
            if (strpos($url, CatalogToolbar::DIRECTION_PARAM_NAME) === false) {
                $url .= sprintf("&%s=%s", CatalogToolbar::DIRECTION_PARAM_NAME, $toolbar->getCurrentDirection());
            }
        }
        
        $data["url"] = $url;
        $data["html"] = $pager->toHtml();
        
        return $data;
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
        
        $limit = $this->getLimit();
        
        settype($limit, "integer");
        
        $currentPage = 1;
        
        $initialCurrentPage = 1;
        
        if(isset($_GET["p"])){
            $initialCurrentPage = $_GET["p"];
            settype($initialCurrentPage, "integer");
            
            $currentPage = $limit * $initialCurrentPage;
        }
        
        $collection->setPage($initialCurrentPage, $limit)->load();
        
        return $currentPage;
//        return $this->getLimit() * ($collection->getCurPage() - 1);
    }
}
