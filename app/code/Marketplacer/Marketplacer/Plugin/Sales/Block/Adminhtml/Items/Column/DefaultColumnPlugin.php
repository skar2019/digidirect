<?php

namespace Marketplacer\Marketplacer\Plugin\Sales\Block\Adminhtml\Items\Column;

use Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn;
use Marketplacer\Marketplacer\Plugin\Sales\Block\AbstractItemOptionsPlugin;

class DefaultColumnPlugin extends AbstractItemOptionsPlugin
{
    /**
     * @param DefaultColumn $block
     * @param array $result
     * @return array
     */
    public function afterGetOrderOptions(DefaultColumn $block, $result)
    {
        $item = $block->getItem();
        $result = $this->addOptions($item, $result);
        return $result;
    }
}
