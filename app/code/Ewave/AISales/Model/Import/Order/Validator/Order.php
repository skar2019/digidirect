<?php
namespace Ewave\AISales\Model\Import\Order\Validator;

use Ewave\AISales\Model\Import\Sales\Validator;

class Order extends Validator
{
    const SHIPPING_METHOD = 'shipping_method';

    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        if (!isset($value[self::SHIPPING_METHOD])) {
            return true;
        }

        $pos = strpos($value[self::SHIPPING_METHOD], '_');
        $lastPosition = strlen($value[self::SHIPPING_METHOD]) - 1;
        if (!$pos or ($lastPosition == $pos)) {
            $this->messages[] = __("Shipping method %1 must have '_' separator", $value[self::SHIPPING_METHOD]);
            return false;
        }

        return true;
    }
}
