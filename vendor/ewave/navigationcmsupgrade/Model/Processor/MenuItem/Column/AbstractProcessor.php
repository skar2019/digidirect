<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem\Column;

use Ewave\NavigationCMSUpgrade\Model\Processor\ResourceModel\MenuItem as MenuItemResource;

/**
 * Class AbstractProcessor
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
class AbstractProcessor
{
    /**
     * @var MenuItem
     */
    protected $menuItemResource;

    /**
     * AbstractProcessor constructor.
     *
     * @param MenuItemResource $menuItem
     */
    public function __construct(MenuItemResource $menuItem)
    {
        $this->menuItemResource = $menuItem;
    }

    /**
     * @param array $data
     * @param string $key
     * @return mixed|null
     */
    protected function getField(array $data, $key)
    {
        return $data[$key] ?? null;
    }
}
