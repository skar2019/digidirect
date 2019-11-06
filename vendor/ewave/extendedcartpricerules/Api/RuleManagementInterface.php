<?php

namespace Ewave\ExtendedCartPriceRules\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface RuleManagementInterface
 * @package Ewave\ExtendedCartPriceRules\Api
 */
interface RuleManagementInterface
{
    /**
     * @param int|null $cartId
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getPaymentMethodLimit($cartId = null);
}
