<?php
namespace Ewave\AISales\Model\Import\Rma\Validator;

use Ewave\AISales\Model\Import\Rma\Model\Eav;
use Ewave\AISales\Model\Import\Rma\Model\Processor;
use Ewave\AISales\Model\Import\Rma\Model\RelationPreparer\Items;

class ItemEavAttributesValidator implements \Zend_Validate_Interface
{
    /**
     * @var Eav
     */
    protected $eav;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * ItemEavAttributesValidator constructor.
     * @param Eav $eav
     */
    public function __construct(
        Eav $eav
    ) {
        $this->eav = $eav;
    }

    /**
     * @param array $rma
     * @return bool
     */
    public function isValid($rma)
    {
        foreach ($rma[Processor::COL_ITEMS] as $item) {
            foreach ($this->eav->getRmaItemAttributes() as $attributeCode => $attribute) {
                if (!array_key_exists($attributeCode, $item)) {
                    $this->messages[] = (string)__('Attribute "%1" is required.', $attributeCode);
                } else if (!empty($attribute['values'])) {
                    $isValueWrong = true;
                    foreach ($attribute['values'] as $value) {
                        if (in_array($item[$attributeCode], $value)) {
                            $isValueWrong = false;
                            break;
                        }
                    }
                    if ($isValueWrong) {
                        $this->messages[] = (string)__('Attribute "%1" has wrong value.', $attributeCode);
                    }
                }
            }
        }

        if (!empty($this->messages)) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
