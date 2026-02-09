<?php
namespace Digidirect\CombinationPricing\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Combinations extends AbstractFieldArray
{
    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('first_sku', [
            'label' => __('First Product SKU'),
            'class' => 'required-entry',
            'style' => 'width:150px'
        ]);
        
        $this->addColumn('second_sku', [
            'label' => __('Second Product SKU'),
            'class' => 'required-entry',
            'style' => 'width:150px'
        ]);
        
        $this->addColumn('fixed_price', [
            'label' => __('Fixed Price'),
            'class' => 'required-entry validate-number validate-zero-or-greater',
            'style' => 'width:100px'
        ]);
        
        $this->addColumn('start_date', [
            'label' => __('Start Date'),
            'class' => 'admin__control-text',
            'style' => 'width:120px'
        ]);
        
        $this->addColumn('end_date', [
            'label' => __('End Date'),
            'class' => 'admin__control-text',
            'style' => 'width:120px'
        ]);
        
        $this->addColumn('active', [
            'label' => __('Active'),
            'renderer' => $this->getActiveRenderer(),
            'style' => 'width:80px'
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Combination');
    }

    /**
     * Get active column renderer
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws LocalizedException
     */
    protected function getActiveRenderer()
    {
        if (!$this->activeRenderer) {
            $this->activeRenderer = $this->getLayout()->createBlock(
                \Digidirect\CombinationPricing\Block\Adminhtml\Form\Field\Column\Active::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->activeRenderer;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $active = $row->getData('active');
        if ($active !== null) {
            $options['option_' . $this->getActiveRenderer()->calcOptionHash($active)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}