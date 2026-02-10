<?php
namespace Digidirect\CombinationPricing\Block\Adminhtml\Form\Field\Column;

class DateTime extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Set input name
     */
    public function setInputName($value)
    {
        return $this->setData('input_name', $value);
    }

    /**
     * Set input id
     */
    public function setInputId($value)
    {
        return $this->setData('input_id', $value);
    }

    /**
     * Render HTML
     */
    protected function _toHtml()
    {
        $column = $this->getData('column');
        $columnName = $this->getData('column_name');
        
        $value = '';
        if (is_array($column) && isset($column[$columnName])) {
            $value = $column[$columnName];
        }

        $html = '<input 
                    type="text" 
                    name="' . $this->escapeHtml($this->getData('input_name')) . '" 
                    id="' . $this->escapeHtml($this->getData('input_id')) . '" 
                    value="' . $this->escapeHtml($value) . '" 
                    class="admin__control-text datetime-picker"
                    placeholder="dd/mm/yyyy hh:mm:ss"
                    style="width: 200px;" />';
        
        return $html;
    }
}