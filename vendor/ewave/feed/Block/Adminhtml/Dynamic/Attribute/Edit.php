<?php

namespace Ewave\Feed\Block\Adminhtml\Dynamic\Attribute;

use \Ewave\Feed\Block\Adminhtml\AbstractEdit as EditContainer;

class Edit extends EditContainer
{
    /**
     * @var string
     */
    protected $_objectId = 'attribute_id';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_dynamic_attribute';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_replaceSaveButtonWithSaveSplitButton();
    }
}
