<?php

namespace Ewave\ExtendedShippingRates\Api;

use Magento\Framework\Model\AbstractModel;

/**
 * Interface CustomConditionInterface
 * @package Ewave\ExtendedShippingRates\Api
 */
interface CustomConditionInterface
{
    /**
     * @return mixed
     */
    public function getLabel();

    /**
     * @param AbstractModel $model
     * @return mixed
     */
    public function validate(AbstractModel $model);
}
