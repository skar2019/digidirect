<?php

namespace Digidirect\Checkout\Plugin;

class DefaultItemPlugin
{

    public function afterGetItemData(
        \Magento\Checkout\CustomerData\AbstractItem $subject,
        $result,
        \Magento\Quote\Model\Quote\Item $item,
        \Magento\Checkout\Helper\Data $checkoutHelper
            )
    {
        $data['item_subtotal'] = ($item->getQty() * 1) * $checkoutHelper->formatPrice($item->getCalculationPrice());

        return \array_merge(
            $result,
            $data
        );
    }

}