<?php
namespace Ewave\CheckoutFields\Model\Condition;

/**
 * Interface ConditionInterface
 * @package Ewave\CheckoutFields\Model
 */
interface ConditionInterface
{
    /**
     * @param string $code
     * @param array $options
     * @return bool
     */
    public function isValid($code, $options = []);
}
