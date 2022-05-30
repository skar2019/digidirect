<?php
namespace Digidirect\Catalog\Plugin\Model;

class Config
{
    public function aroundGetAvailableOrders(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed
    ) {
        $result = $proceed();

        //make sure that each array key does exist, and then remove them
        if (array_key_exists('position', $result)) unset($result['position']);
        if (array_key_exists('name', $result)) unset($result['name']);
        if (array_key_exists('price', $result)) unset($result['price']);

        return $result;
    }
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
        // product name a to z
        $customOption['product_name_asc'] = __('Product Name A-Z');
        // product name z to a
        $customOption['product_name_desc'] = __('Product Name Z-A');
        // price lowest first
        $customOption['price_lowest_first'] = __('Price Lowest First');
        // price highest first
        $customOption['price_highest_first'] = __('Price Highest First');
        // new products
        $customOption['highest_percent_discount'] = __('Highest % Discount');

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