<?php
namespace Digidirect\Navigation\Model\Config\Source;

use Digidirect\Navigation\Model\Menu as MenuModel;

class CustomerStatus implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $return = [

            MenuModel::MENU_ITEM_FOR_ALL_USERS => [
                'value' => MenuModel::MENU_ITEM_FOR_ALL_USERS,
                'label' => __('Does not matter'),
            ],
            MenuModel::MENU_ITEM_FOR_LOGGED_IN => [
                'label' => __('Yes'),
                'value' => MenuModel::MENU_ITEM_FOR_LOGGED_IN
            ],
            MenuModel::MENU_ITEM_FOR_NOT_LOGGED_IN => [
                'label' => __('No'),
                'value' => MenuModel::MENU_ITEM_FOR_NOT_LOGGED_IN
            ],
        ];
        return $return;
    }
}
