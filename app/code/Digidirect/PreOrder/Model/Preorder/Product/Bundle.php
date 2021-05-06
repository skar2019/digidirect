<?php

namespace Digidirect\PreOrder\Model\Preorder\Product;

use Digidirect\PreOrder\Helper\Data as PreOrderHelper;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Bundle
 *
 * @package Digidirect\PreOrder\Model\Preorder\Product
 */
class Bundle extends CompositeAbstract
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * Simple constructor.
     *
     * @param \Digidirect\PreOrder\Helper\Data $preOrderHelper
     * @param \Digidirect\PreOrder\Model\Preorder\Product\Simple $simpleProductPreorder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        PreOrderHelper $preOrderHelper,
        Simple $simpleProductPreorder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepository $productRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;

        parent::__construct($preOrderHelper, $simpleProductPreorder);
    }

    /**
     * {@inheritdoc}
     */
    public function checkProductPreorder(\Magento\Catalog\Api\Data\ProductInterface $product, $requiredQty = null)
    {
        /** @var \Magento\Catalog\Model\Product $product */

        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $product->getTypeInstance();

        $optionIds = [];
        $optionSelectionCounts = [];
        $optionPreorder = [];

        $options = $typeInstance->getOptionsCollection($product);
        foreach ($options as $option) {
            /** @var \Magento\Bundle\Model\Option $option */
            if (!$option->getRequired()) {
                continue;
            }

            $id = $option->getId();
            $optionIds[] = $id;
            $optionSelectionCounts[$id] = 0;
            $optionPreorder[$id] = true;
        }

        if (!$optionIds) {
            return false;
        }

        $selections = $typeInstance->getSelectionsCollection($optionIds, $product);
        $products = $this->getProductsBySelectionsCollection($selections);
        /** @var \Magento\Bundle\Model\Selection $selection */
        foreach ($selections as $selection) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $products[$selection->getProductId()] ?? null;
            if (null === $product) {
                continue;
            }

            $isPreorder = $this->simpleProductPreorder->isProductPreorder($product, $requiredQty);
            $optionId = $selection->getOptionId();
            $optionSelectionCounts[$optionId]++;
            if (!$isPreorder) {
                $optionPreorder[$optionId] = false;
            }
        }

        $result = false;
        foreach ($optionPreorder as $id => $isPreorder) {
            if ($isPreorder && $optionSelectionCounts[$id] > 0) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isQuoteItemPreorder(\Magento\Quote\Api\Data\CartItemInterface $quoteItem, $qtyMultiplier = 1)
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */

        $qty = $quoteItem->getQty() * $qtyMultiplier;
        $isPreorder = false;
        foreach ($quoteItem->getChildren() as $childItem) {
            if (($childItem->getProduct()->isComposite() && $this->isQuoteItemPreorder($childItem, $qty))
                || $this->simpleProductPreorder->isProductPreorder($childItem->getProduct(), $qty)
            ) {
                $isPreorder = true;
                break;
            }
        }
        return $isPreorder;
    }

    /**
     * Get product collection by selection
     *
     * @param  \Magento\Bundle\Model\ResourceModel\Selection\Collection $selections
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    protected function getProductsBySelectionsCollection($selections)
    {
        $productIds = [];
        foreach ($selections as $selection) {
            /** @var \Magento\Bundle\Model\Selection $selection */
            $productIds[] = $selection->getProductId();
        }

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', $productIds, 'in')->create();
        return $this->productRepository->getList($searchCriteria)->getItems();
    }
}
