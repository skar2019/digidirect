<?php

namespace Digidirect\MyOrderItems\Model\ResourceModel\Category;

use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as ItemCollection;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;

/**
 * Class Collection
 * @package Digidirect\MyOrderItems\Model\ResourceModel\Category
 */
class Collection extends CategoryCollection
{
    /**
     * @param ItemCollection $saleItems
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function joinSaleItems(ItemCollection $saleItems)
    {
        $productIds = [];
        $websiteId = $this->_storeManager->getStore($this->getProductStoreId())->getWebsiteId();
        foreach ($saleItems as $saleItem) {
            /* @var \Magento\Sales\Api\Data\OrderItemInterface $saleItem */
            $productIds[] = $saleItem->getProductId();
        }
        
        $this->getSelect()->join(
            ['category_product' => $this->getProductTable()],
            'category_product.category_id = e.entity_id',
            []
        );

        if ($websiteId) {
            $this->getSelect()->join(
                ['w' => $this->getProductWebsiteTable()],
                'category_product.product_id = w.product_id',
                []
            )->where(
                'w.website_id = ?',
                $websiteId
            );
        }
        
        $this->getSelect()->where(
            $this->_conn->quoteInto('category_product.product_id IN(?)', $productIds)
        );
        $this->getSelect()->group('e.entity_id');

        return $this;
    }
}
