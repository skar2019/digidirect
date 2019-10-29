<?php

namespace Ewave\PreOrder\Model\Source;

/**
 * Class Backorders
 *
 * @package Ewave\PreOrder\Model\Source
 */
class Backorders extends \Magento\CatalogInventory\Model\Source\Backorders
{
    /**
     * Empty option value
     */
    const BACKORDERS_NO_ACTIONS = -1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array_merge(
            [['value' => self::BACKORDERS_NO_ACTIONS, 'label' => __('No actions')]],
            parent::toOptionArray()
        );
    }
}
