<?php
namespace Ewave\MagentoFixes\Preference\Magento\Swatches\Block\Product\Renderer;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{
    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        return \Magento\Framework\View\Element\AbstractBlock::getCacheLifetime();
    }
}
