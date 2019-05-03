<?php
namespace Ewave\AISales\Model\Import\Order\Validator;

use Ewave\AISales\Model\Import\Sales\Validator;

class Item extends Validator
{
    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        if (!isset($value[self::ITEMS])) {
            return true;
        }

//        $items = $value[self::ITEMS];
//        foreach ($items as $item) {
//            if (!$this->checkProductRelationExist($item)) {
//                return false;
//            }
//        }

        return true;
    }
}
