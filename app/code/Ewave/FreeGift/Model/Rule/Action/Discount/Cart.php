<?php

namespace Ewave\FreeGift\Model\Rule\Action\Discount;

use Ewave\FreeGift\Api\Data\RuleInterface;

class Cart extends AbstractDiscount
{
    /**
     * @param RuleInterface $freeGiftRule
     * @return bool
     */
    protected function _isFreeGiftHidden(RuleInterface $freeGiftRule)
    {
        return (bool)$freeGiftRule->isHiddenForCustomer();
    }
}
