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
}
