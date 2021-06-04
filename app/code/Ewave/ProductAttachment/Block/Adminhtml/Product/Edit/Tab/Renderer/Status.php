<?php
namespace Ewave\ProductAttachment\Block\Adminhtml\Product\Edit\Tab\Renderer;

/**
 * Class Status
 * @package Ewave\ProductAttachment\Block\Adminhtml\Product\Edit\Tab\Renderer
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renderer for "Action" column in Newsletter templates grid
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        return $row->getStatus() ? __('Enabled') : __('Disabled');
    }
}
