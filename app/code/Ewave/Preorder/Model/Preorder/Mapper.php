<?php

namespace Ewave\PreOrder\Model\Preorder;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Mapper
 *
 * @package Ewave\PreOrder\Model\Preorder
 */
class Mapper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Ewave\PreOrder\Model\Preorder\PreorderModel[]
     */
    protected $preorders;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $preorders
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $preorders
    ) {
        $this->objectManager = $objectManager;
        $this->preorders = $this->processPreordersSettings($preorders);
    }

    /**
     * Get product preorder model
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Ewave\PreOrder\Api\PreorderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductPreorder(\Magento\Catalog\Model\Product $product)
    {
        return $this->getProductPreorderModel($product->getTypeId());
    }

    /**
     * Get product preorder model
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Ewave\PreOrder\Api\PreorderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderItemPreorder(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->getProductPreorder($item->getProduct());
    }

    /**
     * Get product preorder model by product type
     *
     * @param string $type
     * @return \Ewave\PreOrder\Api\PreorderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductPreorderModel($type)
    {
        if (!isset($this->preorders[$type])) {
            throw new NoSuchEntityException(__('There is no Pre-Order for provided type.'));
        }
        return $this->objectManager->get($this->preorders[$type]->getModel());
    }

    /**
     * Parse Pre-Order settings
     *
     * @param \Ewave\PreOrder\Model\Preorder\PreorderModel[] $preorders
     * @return \Ewave\PreOrder\Model\Preorder\PreorderModel[]
     * @throws \Magento\Framework\Exception\InputException
     */
    public function processPreordersSettings($preorders)
    {
        $result = [];

        /** @var \Ewave\PreOrder\Model\Preorder\PreorderModel $preorder */
        foreach ($preorders as $preorder) {
            if (!$preorder->getType() || !$preorder->getModel()) {
                throw new InputException(__('Pre-Order model declaration error.'));
            }

            $result[$preorder->getType()] = $preorder;
        }

        return $result;
    }
}
