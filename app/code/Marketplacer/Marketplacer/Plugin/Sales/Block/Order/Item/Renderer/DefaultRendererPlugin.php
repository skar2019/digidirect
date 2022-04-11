<?php

namespace Marketplacer\Marketplacer\Plugin\Sales\Block\Order\Item\Renderer;

use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;
use Marketplacer\Marketplacer\Plugin\AbstractItemOptionsPlugin;

class DefaultRendererPlugin extends AbstractItemOptionsPlugin
{
    /**
     * @param DefaultRenderer $block
     * @param array $result
     * @return array
     */
    public function afterGetItemOptions(DefaultRenderer $block, $result)
    {
        $item = $block->getOrderItem();
        $result = $this->addOptionsToOrderItemOptions($item, $result, true);
        return $result;
    }
}
