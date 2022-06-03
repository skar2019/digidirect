<?php

namespace Marketplacer\Marketplacer\Plugin\Sales\Block\Order\Email\Items\Order;

use Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder;
use Marketplacer\Marketplacer\Plugin\Sales\Block\AbstractItemOptionsPlugin;

class DefaultOrderPlugin extends AbstractItemOptionsPlugin
{
    /**
     * @param DefaultOrder $block
     * @param array $result
     * @return array
     */
    public function afterGetItemOptions(DefaultOrder $block, $result)
    {
        $item = $block->getItem();
        $result = $this->addOptions($item, $result);
        return $result;
    }
}
