<?php

namespace Ewave\CmsUpgrade\Plugin\Magento\Config\Block\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Ewave\CmsUpgrade\Helper\Data;

/**
 * Class FieldsetPlugin
 * @package Ewave\CmsUpgrade\Plugin\Magento\Config\Block\System\Config\Form
 */
class FieldsetPlugin
{

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * DisableOutput constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * @param Fieldset $subject
     * @param \Closure $proceed
     * @param AbstractElement $element
     * @return string
     */
    public function aroundRender(
        Fieldset $subject,
        \Closure $proceed,
        AbstractElement $element
    ) {
        $result = $proceed($element);
        return $this->drawCheckbox($element) . $result;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function drawCheckbox(AbstractElement $element)
    {
        $result = '';
        if ($this->_helper->isModuleOutputEnabled()) {
            $result =
                '<div style="float: left;"><input type="checkbox" name="generate[' . $element->getHtmlId() . ']"></div>';
        }
        return $result;

    }
}
