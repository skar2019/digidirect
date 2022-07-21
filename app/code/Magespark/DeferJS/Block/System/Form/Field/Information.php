<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark_CopyCmsPageBlock
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */
namespace MageSpark\DeferJS\Block\System\Form\Field;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Information extends Template implements RendererInterface
{
    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $isCheckboxRequired = $this->isInheritCheckboxRequired($element);

        /** Disable element if value is inherited from other scope. Flag has to be set before the value is rendered. */
        if ($element->getInherit() === true && $isCheckboxRequired) {
            $element->setDisabled(true);
        }

        $html = '<td class="label"><label for="' .
            $element->getHtmlId() .
            '">' .
            $element->getLabel() .
            '</label></td>';
        $html .= $this->renderDeferFieldValue($element);

        if ($isCheckboxRequired) {
            $html .= $this->renderInheritCheckbox($element);
        }

        $html .= $this->renderDeferScopeLabel($element);
        $html .= $this->getHint($element);

        return $this->decorateRowHtml($element, $html);
    }

    /**
     * Render element value
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function renderDeferFieldValue(AbstractElement $element)
    {
        $html = '<td class="value">';
        $html .= '<p>- Add attribute <strong style="color:red">nodefer</strong>
                    in <strong style="color:red">&lt;script</strong>
                    for prevent defer.</p>
                    <p>- For Example: <i>&lt;script <b>nodefer</b> type="text/javascript"&gt;....&lt;/script&gt;</i></p>';

        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';
        return $html;
    }

    /**
     * Render inheritance checkbox (Use Default or Use Website)
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function renderInheritCheckbox(AbstractElement $element)
    {
        $htmlId = $element->getHtmlId();
        $namePrefix = preg_replace('#\[value\](\[\])?$#', '', $element->getName());
        $checkedHtml = $element->getInherit() === true ? 'checked="checked"' : '';

        $html = '<td class="use-default">';
        $html .= '<input id="' .
            $htmlId .
            '_inherit" name="' .
            $namePrefix .
            '[inherit]" type="checkbox" value="1"' .
            ' class="checkbox config-inherit" ' .
            $checkedHtml .
            ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" /> ';
        $html .= '<label for="' . $htmlId . '_inherit" class="inherit">' . $this->_getInheritCheckboxLabel(
            $element
        ) . '</label>';
        $html .= '</td>';

        return $html;
    }

    /**
     * Check if inheritance checkbox has to be rendered
     *
     * @param AbstractElement $element
     * @return bool
     */
    protected function isInheritCheckboxRequired(AbstractElement $element)
    {
        return $element->getCanUseWebsiteValue() || $element->getCanUseDefaultValue();
    }

    /**
     * Retrieve label for the inheritance checkbox
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getInheritCheckboxLabel(AbstractElement $element)
    {
        $checkboxLabel = __('Use Default');
        if ($element->getCanUseWebsiteValue()) {
            $checkboxLabel = __('Use Website');
        }
        return $checkboxLabel;
    }

    /**
     * Render scope label
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function renderDeferScopeLabel(AbstractElement $element)
    {
        $html = '<td class="scope-label">';
        if ($element->getScope() && false === $this->_storeManager->isSingleStoreMode()) {
            $html .= $element->getScopeLabel();
        }
        $html .= '</td>';
        return $html;
    }

    /**
     * Render field hint
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function getHint(AbstractElement $element)
    {
        $html = '<td class="">';
        if ($element->getHint()) {
            $html .= '<div class="hint"><div style="display: none;">' . $element->getHint() . '</div></div>';
        }
        $html .= '</td>';
        return $html;
    }

    /**
     * Decorate field row html
     *
     * @param AbstractElement $element
     * @param string $html
     * @return string
     */
    protected function decorateRowHtml($element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
    }
}
