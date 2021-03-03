<?php

namespace Digidirect\Navigation\Model\Config\Source;

use Digidirect\Navigation\Model\ResourceModel\Menu\CollectionFactory as MenuCollectionFactory;
use Digidirect\Navigation\Model\ResourceModel\Menu\Collection as MenuCollection;
use Magento\Framework\Registry as Registry;
use Digidirect\Navigation\Model\Registry\Constants;

class MenuItems implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var MenuCollection
     */
    protected $menuFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * MenuItems constructor.
     * @param MenuCollectionFactory $menuCollectionFactory
     * @param Registry $registry
     */
    public function __construct(
        MenuCollectionFactory $menuCollectionFactory,
        Registry $registry
    ) {
        $this->menuFactory = $menuCollectionFactory->create();
        $this->registry = $registry;
    }

    /**
     * Get all menu items except current
     *
     * @return []
     */
    public function toOptionArray()
    {
        if ($menuItem = $this->registry->registry(Constants::CURRENT_MENU_ITEM)) {
            $this->menuFactory->addFieldToFilter('entity_id', ['nin' => $menuItem->getId()]);
        }
        $emptyOption = ['' => ['value' => 0, 'label' => __('No Parent Item')]];
        $options = $this->menuFactory->toOptionArray();
        $result = $emptyOption + $options;
        return $result;
    }
}
