<?php
namespace Ewave\AISales\Model\Import\Order\Validator;

use Ewave\AISales\Model\Import\AbstractValidator;

class ShippingAddress extends AbstractValidator
{
    const SHIPPING_ADDRESS = 'shipping_address';
    const IS_VIRTUAL = 'is_virtual';
    const SHIPPING_METHOD = 'shipping_method';

    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        if (!empty($value[self::SHIPPING_ADDRESS])) {
            if ($value[self::IS_VIRTUAL]) {
                $this->messages[] = __(
                    'Order with property "%1" cannot have "%2"',
                    self::IS_VIRTUAL,
                    self::SHIPPING_ADDRESS
                );

                return false;
            }

            if (!isset($value[self::SHIPPING_METHOD])) {
                $this->messages[] = __('Specify "%1" for order', self::SHIPPING_METHOD);

                return false;
            }
        }

        if (!$value[self::IS_VIRTUAL]) {
            if (empty($value[self::SHIPPING_ADDRESS])) {
                $this->messages[] = __(
                    'Not virtual order must have "%1"',
                    self::SHIPPING_ADDRESS
                );

                return false;
            }
        }

        return true;
    }
}
