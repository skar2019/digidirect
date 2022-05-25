<?php

namespace Marketplacer\Marketplacer\Plugin\Sales\Block\Order\Email\Items;

use Magento\Sales\Block\Order\Email\Items\DefaultItems;
use Marketplacer\Marketplacer\Plugin\Sales\Block\AbstractItemOptionsPlugin;

class DefaultItemsPlugin extends AbstractItemOptionsPlugin
{
    /**
     * @param DefaultItems $block
     * @param array $result
     * @return array
     */
    public function afterGetItemOptions(DefaultItems $block, $result)
    {
        $item = $block->getItem()->getOrderItem();
        $result = $this->addOptions($item, $result);
        return $result;
    }
}
