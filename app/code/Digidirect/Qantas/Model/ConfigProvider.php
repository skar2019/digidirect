<?php

/**
 * Created By : Rohan Hapani
 */

namespace Digidirect\Qantas\Model;

use \Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

class ConfigProvider implements ConfigProviderInterface {

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function getConfig() {
        $items = $this->checkoutSession->getQuote()->getAllItems();

        $qffPoints = [];
        $config = [];
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
                    $totalPoints = $totals * 1;
                    $qffPoints[] = $totalPoints;
                }
            }
            $qffTotalPoints = array_sum($qffPoints);
        }

        $config['qantas_total_points'] = number_format($qffTotalPoints);
        $config['qantas_bonus_points'] = $qffBonusPoints;
        $config['qantas_base_points'] = $qffBase;
        return $config;
    }
}
