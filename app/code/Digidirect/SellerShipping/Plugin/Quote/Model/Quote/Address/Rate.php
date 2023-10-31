<?php

declare(strict_types=1);

namespace Digidirect\SellerShipping\Plugin\Quote\Model\Quote\Address;

use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\Quote\Address\RateResult\AbstractResult;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class RatePlugin
{
    public function afterImportShippingRate(
        Rate $subject,
        Rate $result,
        AbstractResult $rate
    ): Rate {
        if ($rate instanceof Method) {
            // some logic to checks
            // set custom price to method
            $result->setPrice($rate->getPrice() + 50);
        }

        return $result;
    }
}