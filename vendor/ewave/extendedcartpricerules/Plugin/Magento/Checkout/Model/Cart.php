<?php

namespace Ewave\ExtendedCartPriceRules\Plugin\Magento\Checkout\Model;

/**
 * Class Cart
 * @package Ewave\ExtendedCartPriceRules\Plugin\Magento\Checkout\Model
 */
class Cart
{
    /**
     * @var array
     */
    protected $beforeUpdateItemsInfo = [];

    /**
     * @param \Magento\Checkout\Model\Cart $subject
     * @param array $data
     * @return array
     */
    public function beforeUpdateItems(\Magento\Checkout\Model\Cart $subject, $data)
    {
        foreach ($data as $itemId => $itemInfo) {
            $item = $subject->getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }
            $this->beforeUpdateItemsInfo[$itemId] = [
                'qty' => $item->getQty(),
                'updated' => $itemInfo['qty'] != $item->getQty()
            ];
        }
        return [$data];
    }

    /**
     * @return array
     */
    public function getBeforeUpdateItemsInfo()
    {
        return $this->beforeUpdateItemsInfo;
    }
}
