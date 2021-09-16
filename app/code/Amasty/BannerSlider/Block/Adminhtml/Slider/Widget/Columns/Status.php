<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Block\Adminhtml\Slider\Widget\Columns;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Widget\Block\BlockInterface;

class Status extends AbstractRenderer implements BlockInterface
{
    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        if ($this->getColumn()->getEditable()) {
            $result = '<div class="admin__grid-control">';
            $result .= $this->getColumn()->getEditOnly() ? ''
                : '<span class="admin__grid-control-value">' . $this->_getValue($row) . '</span>';

            return $result . $this->_getInputValueElement($row) . '</div>';
        }
        return $this->prepareValue($this->_getValue($row));
    }

    private function prepareValue(string $value): string
    {
        return $value
            ? '<span class="grid-severity-notice">' . __('Enabled') . '</span>'
            : '<span class="grid-severity-critical">' . __('Disabled') . '</span>';
    }
}
