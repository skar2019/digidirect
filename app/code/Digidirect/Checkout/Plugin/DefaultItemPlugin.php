<?php

namespace Digidirect\Checkout\Plugin;

class DefaultItemPlugin
{

    public function afterGetItemData(
        \Magento\Checkout\CustomerData\AbstractItem $subject,
        $result,
        \Magento\Quote\Model\Quote\Item $item)
    {
        $data['item_subtotal'] = "Hello World!";

        return \array_merge(
            $result,
            $data
        );
    }

}