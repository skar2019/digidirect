<?php

namespace Marketplacer\Marketplacer\Plugin\Checkout\Block\Cart\Item;

use Magento\Checkout\Block\Cart\Item\Renderer;
use Marketplacer\Marketplacer\Plugin\AbstractItemOptionsPlugin;

class RendererPlugin extends AbstractItemOptionsPlugin
{
    /**
     * @param Renderer $renderer
     * @param array $result
     * @return array
     */
    public function afterGetOptionList(Renderer $renderer, $result)
    {
        $item = $renderer->getItem();
        $result = $this->addOptionsToQuoteItemOptions($item, $result, true);
        return $result;
    }
}
