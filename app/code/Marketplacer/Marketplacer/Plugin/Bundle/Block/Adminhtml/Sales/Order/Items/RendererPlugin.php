<?php

namespace Marketplacer\Marketplacer\Plugin\Bundle\Block\Adminhtml\Sales\Order\Items;

use Magento\Bundle\Block\Adminhtml\Sales\Order\Items\Renderer;
use Marketplacer\Marketplacer\Plugin\AbstractItemOptionsPlugin;

class RendererPlugin extends AbstractItemOptionsPlugin
{
    /**
     * @param Renderer $block
     * @param array $result
     * @return array
     */
    public function afterGetOrderOptions(Renderer $block, $result)
    {
        $item = $block->getOrderItem();
        $result = $this->addOptionsToOrderItemOptions($item, $result);
        return $result;
    }
}
