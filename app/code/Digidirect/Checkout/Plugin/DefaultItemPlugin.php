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
        $data['qantas_points_new'] = round(($item->getQty() * 1) * ($item->getCalculationPrice()));
        
        return \array_merge(
            $result,
            $data
        );
    }
    
    public function getQffPoints() {

        $qty = $item->getQty() * 1;

        if ($item->getPrice() == 0) {

            $promotionPoints = 0;
        } else {

            $finalProductPrice = $item->getProduct()->getFinalPrice();

            if ($item->getProduct()->offsetExists("qff_bonus_points") && $item->getProduct()->offsetExists("qff_base")) {

                $qff_bonus_points = $item->getProduct()->getQffBonusPoints();

                $qff_base_points = $item->getProduct()->getQffBase();

                $pointsSum = $qff_bonus_points + $qff_base_points;

                $promotionPoints = number_format($pointsSum * ($qty * $finalProductPrice));
            } else {
                if ($item->getProduct()->offsetExists("qff_base")) {

                    $qff_base_points = $item->getProduct()->getQffBase();

                    $promotionPoints = number_format($qff_base_points * ($qty * $finalProductPrice));
                } else {

                    $promotionPoints = number_format(($qty * $finalProductPrice) * 2);
                }
            }
        }
        return $promotionPoints;
    }

}