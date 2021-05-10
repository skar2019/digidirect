<?php

namespace Digidirect\PreOrder\Block\Product\View\Preorder;

/**
 * Class ProductBundle
 *
 * @package Digidirect\PreOrder\Block\Product\View\Preorder
 */
class ProductBundle extends ProductAbstract
{
    /**
     * @var []
     */
    protected $_bundleOptionsData;

    /**
     * @var []
     */
    protected $_bundleSelectionsData;

    /**
     * @var bool
     */
    protected $_isAllProductsPreorder;

    /**
     * Get bundle selections data
     *
     * @return []
     */
    public function getBundleSelectionsData()
    {
        if (null === $this->_bundleSelectionsData) {
            $this->prepareBundleData();
        }
        return $this->_bundleSelectionsData;
    }

    /**
     * Get bundle options data
     *
     * @return []
     */
    public function getBundleOptionsData()
    {
        if (null === $this->_bundleOptionsData) {
            $this->prepareBundleData();
        }
        return $this->_bundleOptionsData;
    }

    /**
     * Is all products PreOrder
     *
     * @return bool
     */
    public function isAllProductsPreorder()
    {
        if (null === $this->_isAllProductsPreorder) {
            $this->prepareBundleData();
        }
        return $this->_isAllProductsPreorder;
    }

    /**
     * Get map
     *
     * @return []
     */
    public function getMap()
    {
        $selectionsPreorderMap = [];
        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $this->getProduct()->getTypeInstance();
        $optionIds = $typeInstance->getOptionsIds($this->getProduct());
        $selections = $typeInstance->getSelectionsCollection($optionIds, $this->getProduct());
        $productIds = [];
        foreach ($selections as $selection) {
            $productIds[] = $selection->getProductId();
        }
        $products = $this->getProduct()->getCollection()->addFieldToFilter('entity_id', $productIds);
        foreach ($selections as $selection) {
            /** @var \Magento\Catalog\Model\Product $product */
            /** @var \Magento\Bundle\Model\Selection $selection */
            $product = $products->getItemById($selection->getProductId());

            $isPreorder = $this->preOrderHelper->isProductPreorder($product);
            if (!$isPreorder) {
                continue;
            }
            $selectionsPreorderMap[$selection->getOptionId() . '-' . $selection->getSelectionId()] = [
                'note' => $this->preOrderHelper->getProductPreorderNote($product),
            ];
        }
        return $selectionsPreorderMap;
    }

    /**
     * Prepare bundle data
     *
     * @return void
     */
    protected function prepareBundleData()
    {
        $this->_bundleSelectionsData = [];
        $this->_bundleOptionsData = [];

        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $this->getProduct()->getTypeInstance();

        $optionIds = $typeInstance->getOptionsIds($this->getProduct());
        $options = $typeInstance->getOptions($this->getProduct());
        foreach ($options as $option) {
            /** @var $option \Magento\Bundle\Model\Option */
            $this->_bundleOptionsData[$option->getId()] = [
                'isSingle'         => null,
                'isMultiSelection' => (bool)$option->isMultiSelection(),
                'isRequired'       => (bool)$option->getRequired(),
                'selectionCount'   => 0, // for a while
                'isPreorder'       => null,
                'message'          => null,
                'selectionId'      => 0,
            ];
        }

        $selections = $typeInstance->getSelectionsCollection($optionIds, $this->getProduct());
        $productIds = [];
        foreach ($selections as $selection) {
            $productIds[] = $selection->getProductId();
        }
        $products = $this->getProduct()->getCollection()->addFieldToFilter('entity_id', $productIds);
        $this->_isAllProductsPreorder = true;
        foreach ($selections as $selection) {
            /** @var \Magento\Catalog\Model\Product $product */
            /** @var \Magento\Bundle\Model\Selection $selection */
            $product = $products->getItemById($selection->getProductId());

            $isPreorder = $this->preOrderHelper->isProductPreorder($product);
            if (!$isPreorder) {
                $this->_isAllProductsPreorder = false;
            }

            $note = $this->preOrderHelper->getProductPreorderNote($product);
            $cartLabel = $this->preOrderHelper->getProductPreorderCartLabel($product);

            $this->_bundleSelectionsData[$selection->getSelectionId()] = [
                'isPreorder' => $isPreorder,
                'note'       => $note,
                'cartLabel'  => $cartLabel,
                'optionId'   => $selection->getOptionId(),
            ];

            // Update option record
            $optionRecord = &$this->_bundleOptionsData[$selection->getOptionId()];
            $optionRecord['selectionCount']++;
            $optionRecord['isSingle'] = $optionRecord['selectionCount'] == 1;

            if ($optionRecord['isSingle']) {
                $optionRecord['isPreorder'] = $isPreorder;
                $optionRecord['message'] = $note;
                $optionRecord['selectionId'] = $selection->getSelectionId();
            } else {
                // Have to analyze selections on frontend in order to find out
                $optionRecord['isPreorder'] = null;
                $optionRecord['message'] = null;
            }
        }
    }
}
