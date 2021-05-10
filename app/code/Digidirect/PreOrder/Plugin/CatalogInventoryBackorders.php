<?php

namespace Digidirect\PreOrder\Plugin;

/**
 * Class CatalogInventoryBackorders
 *
 * @package Digidirect\PreOrder\Plugin
 */
class CatalogInventoryBackorders
{
    /**
     * Add "Allow Pre-Order" option to "Backorders" field on product inventory page
     *
     * @param \Magento\CatalogInventory\Model\Source\Backorders $subject
     * @param  [] $optionArray
     * @return []
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToOptionArray(
        \Magento\CatalogInventory\Model\Source\Backorders $subject,
        array $optionArray
    ) {
        $optionArray[] = [
            'value' => \Digidirect\PreOrder\Helper\Data::BACKORDERS_PREORDER_OPTION,
            'label' => __('Allow Pre-Orders')
        ];
        return $optionArray;
    }
}
