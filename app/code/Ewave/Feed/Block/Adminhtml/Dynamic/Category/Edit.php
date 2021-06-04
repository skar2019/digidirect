<?php

namespace Ewave\Feed\Block\Adminhtml\Dynamic\Category;

use \Ewave\Feed\Block\Adminhtml\AbstractEdit as EditContainer;

class Edit extends EditContainer
{
    /**
     * @var string
     */
    protected $_objectId = 'mapping_id';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_dynamic_category';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_replaceSaveButtonWithSaveSplitButton();
    }
}
