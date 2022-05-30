<?php
namespace Digidirect\Catalog\Plugin\Model;

class Config
{
    /**
     * Add custom Sort By option
     *
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param array $options
     * @return array []
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
    {
        // nmost viewed
        $customOption['most_viewed'] = __('Most Viewed');
        // new products
        $customOption['latest'] = __('New Products');

        // merge default sorting options with custom options
        $options = array_merge($customOption, $options);

        return $options;
    }

    /**
     * This method is optional. Use it to set Most Viewed as the default
     * sorting option in the category view page
     *
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetProductListDefaultSortBy(\Magento\Catalog\Model\Config $catalogConfig)
    {
        return 'most_viewed';
    }
}