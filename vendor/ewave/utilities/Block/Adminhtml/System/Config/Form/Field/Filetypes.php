<?php

namespace Ewave\Utilities\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Filetypes
 * @package Ewave\Utilities\Block\Adminhtml\System\Config\Form\Field
 */
class Filetypes extends AbstractFieldArray
{
    /**
     * Prepare to render
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('extension', [
                'label' => __('Allowed upload file types for WYSIWYG'),
            ]);
        $this->_addAfter = false;
    }
}
