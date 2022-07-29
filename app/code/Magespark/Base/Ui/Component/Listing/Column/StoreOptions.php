<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */


namespace MageSpark\Base\Ui\Component\Listing\Column;

use Magento\Store\Ui\Component\Listing\Column\Store\Options;

/**
 * Class StoreOptions
 *
 * To use this class, add <item name="options" xsi:type="object">MageSpark\Base\Ui\Component\Listing\Column\StoreOptions</item>
 * to your ui field as argument
 *
 * @package MageSpark\Base\Ui\Component\Listing\Column
 */
class StoreOptions extends Options
{
    /**
     * All Store Views value
     */
    const ALL_STORE_VIEWS = '0';

    /**
     * Convert store view to option array
     * Get options
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->currentOptions['All Store Views']['label'] = __('All Store Views');
        $this->currentOptions['All Store Views']['value'] = self::ALL_STORE_VIEWS;

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
