<?php

namespace Marketplacer\Marketplacer\Plugin\Sales\Model\Order\Pdf\Items;

use Magento\Sales\Model\Order\Pdf\Items\AbstractItems;
use Marketplacer\Marketplacer\Plugin\Sales\Block\AbstractItemOptionsPlugin;

class AbstractItemsPlugin extends AbstractItemOptionsPlugin
{
    /**
     * @param AbstractItems $block
     * @param array $result
     * @return array
     */
    public function afterGetItemOptions(AbstractItems $block, $result)
    {
        $item = $block->getItem()->getOrderItem();
        $result = $this->addOptions($item, $result);
        return $result;
    }
}
