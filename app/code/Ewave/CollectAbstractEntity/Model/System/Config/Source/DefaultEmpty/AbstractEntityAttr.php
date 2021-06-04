<?php
namespace Ewave\CollectAbstractEntity\Model\System\Config\Source\DefaultEmpty;

use Ewave\CollectAbstractEntity\Model\System\Config\Source\AbstractEntityAttr as AbstractEntityAttrParent;

/**
 * Class AbstractEntityAttr
 * @package Ewave\CollectAbstractEntity\Model\System\Config\Source\DefaultEmpty
 */
class AbstractEntityAttr extends AbstractEntityAttrParent
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $emptyValue = [['value' => '', 'label' => '']];

        return array_merge($emptyValue, parent::toOptionArray());
    }
}
