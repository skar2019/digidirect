<?php

namespace Marketplacer\Marketplacer\Plugin\Bundle\Block\Sales\Order\Items;

use Magento\Bundle\Block\Sales\Order\Items\Renderer;
use Marketplacer\Marketplacer\Plugin\AbstractItemOptionsPlugin;

class RendererPlugin extends AbstractItemOptionsPlugin
{
    /**
     * @param Renderer $block
     * @param array $result
     * @return array
     */
    public function afterGetItemOptions(Renderer $block, $result)
    {
        $useLinks = true;

        $handles = $block->getLayout()->getUpdate()->getHandles();
        foreach ($handles as $handle) {
            if (strpos($handle, 'sales_email_') === 0) {
                $useLinks = false;
                break;
            }
        }

        $item = $block->getOrderItem();
        $result = $this->addOptionsToOrderItemOptions($item, $result, $useLinks);
        return $result;
    }
}
