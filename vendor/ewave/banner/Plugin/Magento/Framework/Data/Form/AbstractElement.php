<?php
namespace Ewave\Banner\Plugin\Magento\Framework\Data\Form;

class AbstractElement
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetHtmlId(\Magento\Framework\Data\Form\Element\AbstractElement $element, $result)
    {
        $position = strpos($result, 'ewave_grid_widget_product_id');
        if ($position) {
            $result = substr($result, $position);
        }
        return $result;
    }
}
