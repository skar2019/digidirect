<?php
namespace Digidirect\CombinationPricing\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Combinations extends AbstractFieldArray
{
    /**
     * @var \Digidirect\CombinationPricing\Block\Adminhtml\Form\Field\Column\DateTime
     */
    private $dateTimeRenderer;

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
        
        $this->addColumn('start_datetime', [
            'label' => __('Start Date & Time'),
            'renderer' => $this->getDateTimeRenderer(),
            'style' => 'width:180px'
        ]);
        
        $this->addColumn('end_datetime', [
            'label' => __('End Date & Time'),
            'renderer' => $this->getDateTimeRenderer(),
            'style' => 'width:180px'
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Combination');
    }

    /**
     * Get datetime column renderer
     *
     * @return \Digidirect\CombinationPricing\Block\Adminhtml\Form\Field\Column\DateTime
     * @throws LocalizedException
     */
    private function getDateTimeRenderer()
    {
        if (!$this->dateTimeRenderer) {
            $this->dateTimeRenderer = $this->getLayout()->createBlock(
                \Digidirect\CombinationPricing\Block\Adminhtml\Form\Field\Column\DateTime::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->dateTimeRenderer;
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
        $row->setData('option_extra_attrs', $options);
    }
}