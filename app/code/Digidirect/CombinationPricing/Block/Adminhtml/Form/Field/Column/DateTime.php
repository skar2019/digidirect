<?php
namespace Digidirect\CombinationPricing\Block\Adminhtml\Form\Field\Column;

use Magento\Framework\View\Element\AbstractBlock;

class DateTime extends AbstractBlock
{
    /**
     * Set input name
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setData('input_name', $value);
    }

    /**
     * Set input id
     *
     * @param string $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setData('input_id', $value);
    }

    /**
     * Set column name
     *
     * @param string $value
     * @return $this
     */
    public function setColumnName($value)
    {
        return $this->setData('column_name', $value);
    }

    /**
     * Set column value
     *
     * @param string $value
     * @return $this
     */
    public function setColumn($value)
    {
        return $this->setData('column', $value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $inputName = $this->getData('input_name');
        $inputId = $this->getData('input_id');
        $columnName = $this->getData('column_name');
        $column = $this->getData('column');
        
        $value = '';
        if (is_array($column) && isset($column[$columnName])) {
            $value = $column[$columnName];
        }

        $html = '<input type="text" 
                    name="' . $inputName . '" 
                    id="' . $inputId . '" 
                    value="' . $this->escapeHtml($value) . '" 
                    class="admin__control-text input-text" 
                    placeholder="YYYY-MM-DD HH:MM:SS"
                    style="width:180px" />';
        
        return $html;
    }
}