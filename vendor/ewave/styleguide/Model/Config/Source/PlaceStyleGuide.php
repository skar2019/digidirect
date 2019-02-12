<?php
namespace Ewave\StyleGuide\Model\Config\Source;

/**
 * @api
 */
class PlaceStyleGuide implements \Magento\Framework\Option\ArrayInterface
{
    // TODO: https://ewave.tpondemand.com/entity/278416-as-an-extension-i-want-to
    const VALUE_DISABLED = 'disable';
    const VALUE_SEPARATE_PAGE = 'separate_page';
    const VALUE_IN_CONTEXT = 'enable';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::VALUE_DISABLED, 'label' => __('Disable')],
            ['value' => self::VALUE_SEPARATE_PAGE, 'label' => __('Separate page only')],
            ['value' => self::VALUE_IN_CONTEXT, 'label' => __('Enable')]
        ];
    }
}
