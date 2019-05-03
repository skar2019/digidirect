<?php
namespace Ewave\AISales\Model\Import\Sales;

class OrderRelationItemValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        if (!empty($value[self::ITEMS])) {
            foreach ($value[self::ITEMS] as $item) {
                if (!$this->checkOrderItemRelationExist($item) or !$this->checkProductRelationExist($item)) {
                    return false;
                }
            }
        }

        return true;
    }
}
