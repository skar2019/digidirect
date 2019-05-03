<?php
namespace Ewave\AISales\Model\Import\Sales;

class OrderRelationValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        return $this->checkOrderRelationExist($value);
    }
}
