<?php
namespace Digidirect\MagentoFixes\Plugin\Magento\Catalog\Block\Product\View;

/**
 * Class OptionsPlugin
 * @package Digidirect\MagentoFixes\Plugin\Magento\Catalog\Block\Product\View
 */
class OptionsPlugin
{
    /**
     * This is the fix to prevent "warning" in template:
     * vendor/magento/module-catalog/view/frontend/templates/product/view/options.phtml
     * where paramentr of count() method is null
     *
     * @param object $subject
     * @param array|null $array
     * @param string $prefix
     * @param bool $forceSetAll
     * @return array
     */
    public function beforeDecorateArray($subject, $array, $prefix = 'decorated_', $forceSetAll = false)
    {
        if ($array === null) {
            $array = [];
        }

        return [$array, $prefix, $forceSetAll];
    }
}
