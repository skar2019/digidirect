<?php

namespace Digidirect\OnSaleProducts\Model;

class Rule extends \Magento\CatalogRule\Model\Rule {

    protected $_productIds;
/**
     * Get array of product ids which are matched by rule
     *
     * @return array
     */
    public function getListProductIdsInRule() {
        $productCollection = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->setOrder('entity_id','desc');

        $productFactory = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Catalog\Model\ProductFactory');

        $this->_productIds = [];
        $this->setCollectedAttributes([]);

        $this->getConditions()->collectValidatedAttributes($productCollection);

        \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Framework\Model\ResourceModel\Iterator')->walk(
            $productCollection->getSelect()->limit(15),
        [
            [$this, 'callbackValidateProduct']
        ],
        [
            'attributes' => $this->getCollectedAttributes(),
            'product' => $productFactory->create()
        ]);

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct($args) {
        $product = clone $args['product'];
        $product->setData($args['row']);
        $websites = $this->_getWebsitesMap();
        foreach ($websites as $websiteId => $defaultStoreId) {
            $product->setStoreId($defaultStoreId);
            if ($this->getConditions()->validate($product)) {
                 $this->_productIds[] = $product->getId();
            }
        }
    }

    /**
     * Prepare website map
     *
     * @return array
     */
    protected function _getWebsitesMap() {
        $map = [];
        $websites = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Store\Model\StoreManagerInterface')->getWebsites();

        foreach ($websites as $website) {
            if ($website->getDefaultStore() === null) {
                continue;
            }

            $map[$website->getId()] = $website->getDefaultStore()->getId();
        }

        return $map;
    }
}

?>
