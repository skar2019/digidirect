<?php
namespace Ewave\AISales\Model\Import\Order\Validator;

use Ewave\AISales\Model\Import\AbstractValidator;
use Magento\Sales\Api\Data\OrderAddressInterface;

class BillingAddress extends AbstractValidator
{
    const SALES_ORDER_ADDRESS = 'sales_order_address';
    const BILLING_ADDRESS = 'billing_address';
    const BILLING_ADDRESS_ID = 'billing_address_id';

    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        if (!$this->isSpecifiedOnlyOne($value)) {
            $this->messages[] = __(
                'Only one "%1" or "%2" can be specified',
                self::BILLING_ADDRESS_ID,
                self::BILLING_ADDRESS
            );

            return false;
        }

        if (isset($value[self::BILLING_ADDRESS_ID])) {
            $isAddressExist = $this->dbHelper->isEntityExist(
                self::SALES_ORDER_ADDRESS,
                [OrderAddressInterface::ENTITY_ID => $value]
            );
            if (!$isAddressExist) {
                $this->messages[] = __(
                    'Billing Address with "%1 = %2" does not exist',
                    self::BILLING_ADDRESS_ID,
                    $value[self::BILLING_ADDRESS_ID]
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Is specified only billing_address_id or billing_address
     *
     * @param array $value
     * @return bool
     */
    protected function isSpecifiedOnlyOne($value)
    {
        return !(isset($value[self::BILLING_ADDRESS_ID]) && !empty($value[self::BILLING_ADDRESS]));
    }
}
