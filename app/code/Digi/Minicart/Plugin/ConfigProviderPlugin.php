<?php
namespace Digi\Minicart\Plugin;

class ConfigProviderPlugin extends \Magento\Framework\Model\AbstractModel
{

    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, array $result)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

        $items = $cart->getQuote()->getAllItems();

        $qffPoints = [];

        foreach ($items as $item) {

            $qty = $item->getQty();
            $price = $item->getPrice();
            $total = $price * $qty;
            $totals = str_replace('"', "", $total);
            $qffBase = $item->getProduct()->getQffBase();
            $qffBonusPoints = $item->getProduct()->getQffBonusPoints();


            if ($qffBase !== NULL && $qffBonusPoints !== NULL) {
                $sumOfPoints = $qffBase + $qffBonusPoints;
                $totalPoints = $totals * $sumOfPoints;
                $qffPoints[] = $totalPoints;
            } else {
                if ($qffBase != NULL) {
                    $totalPoints = $qffBase * $total;
                    $qffPoints[] = $totalPoints;
                } else {
                    $totalPoints = $totals * 2;
                    $qffPoints[] = $totalPoints;
                }
            }
            $qffTotalPoints = array_sum($qffPoints);
        }
        
        
        $result['qffPromotionPoints'] = number_format($qffTotalPoints);
        return $result;
    }

}