<?php
namespace Digidirect\InfiniteScroll\Block\Product\ProductList;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * Return pager instance
     *
     * @return \Magento\Theme\Block\Html\Pager
     */
    public function getPager()
    {
        $pagerBlock = $this->getChildBlock('infinitescroll_product_list_toolbar_pager');

        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            /* @var $pagerBlock \Magento\Theme\Block\Html\Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());
            
            if (!$this->getCollection()) {
                $this->setCollection($this->getParentBlock()->getLoadedProductCollection());
            }
            
            $initialCurrentPage = 1;
            
            if(isset($_GET["p"])){
                $initialCurrentPage = $_GET["p"];
                settype($initialCurrentPage, "integer");
            }

            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );

            return $pagerBlock;
        }

        return false;
    }

    /**
     * Set infinite scroll collection limit
     * 
     * @param int $limit
     * @return $this
     */
    public function setInfiniteScrollLimit($limit)
    {
        $this->setData('_current_limit', $limit);
        return $this;
    }
    
    public function nextPage()
    {
        $initialCurrentPage = 1;
        if(isset($_GET["p"])){
            $initialCurrentPage = $_GET["p"];
            settype($initialCurrentPage, "integer");
        }
        
        $this->_collection->load();
        $this->_collection->setCurPage($initialCurrentPage);
        $this->_collection->setPageSize($this->getLimit());
        
//        echo "Current Order: " . $this->getCurrentOrder() . "<br />";
//        echo "Current Direction: " . $this->getCurrentDirection() . "<br />";
        
//        if (($this->getCurrentOrder()) == 'position') {
//            $this->_collection->addAttributeToSort(
//                $this->getCurrentOrder(),
//                $this->getCurrentDirection()
//            );
//            
//            echo "HERE IF" . "<br/>";
//        } else {
//            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
//            echo "HERE ELSE" . "<br/>";
//        }
        
        $this->_collection->clear();
        
        $pagerBlock = $this->getChildBlock('infinitescroll_product_list_toolbar_pager');
        
        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCurPage(
                $initialCurrentPage
            )->setCollection(
                $this->_collection
            );
        }


        return $pagerBlock->toHtml();
        
//        $i = 1;
//        foreach($this->_collection as $item){
//            echo $i . ') ' . $item->getName() . "<br/>";
//            $i++;
//        }
    }
    
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        
        $initialCurrentPage = 1;
            
        if(isset($_GET["p"])){
            $initialCurrentPage = $_GET["p"];
            settype($initialCurrentPage, "integer");
        }

        $this->_collection->setCurPage($initialCurrentPage);

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        
        if ($this->getCurrentOrder()) {
            if (($this->getCurrentOrder()) == 'position') {
                $this->_collection->addAttributeToSort(
                    $this->getCurrentOrder(),
                    $this->getCurrentDirection()
                );
            } else {
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            }
        }
        
        return $this;
    }
}
