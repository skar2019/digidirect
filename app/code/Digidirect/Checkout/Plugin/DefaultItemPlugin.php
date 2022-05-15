<?php

namespace Digidirect\Checkout\Plugin;

class DefaultItemPlugin
{

    public function afterGetItemData(
        \Magento\Checkout\CustomerData\AbstractItem $subject,
        $result,
        \Magento\Quote\Model\Quote\Item $item
            )
    {
        $data['item_subtotal'] = '$'.number_format((float)(($item->getQty() * 1) * ($item->getCalculationPrice())), 2, '.', '');
        $data['qantas_points_new'] = 'Testing Qantas Points';
        
        return \array_merge(
            $result,
            $data
        );
    }

}