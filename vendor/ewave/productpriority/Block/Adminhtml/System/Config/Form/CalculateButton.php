<?php
namespace Ewave\ProductPriority\Block\Adminhtml\System\Config\Form;

/**
 * Class CalculateButton
 * @package Ewave\ProductPriority\Block\Adminhtml\System\Config\Form
 */
class CalculateButton extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $button = $this->_createButton();
        $button->addData(
            [
                'label' => __('Recalculate'),
                'id' => 'recalculate',
                'class' => 'priority-recalculate save primary',
                'type' => 'button'
            ]
        );
        return $button->toHtml();
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _createButton()
    {
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button',
            'start_calculate'
        );
    }
}
