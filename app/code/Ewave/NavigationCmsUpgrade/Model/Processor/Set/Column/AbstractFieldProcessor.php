<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\Set\Column;

use Ewave\NavigationCMSUpgrade\Model\Processor\ResourceModel\Set as MenuSetResource;

/**
 * Class AbstractFieldProcessor
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor\Set
 */
class AbstractFieldProcessor implements FieldProcessorInterface
{
    /**
     * @var MenuSetResource
     */
    protected $menuSetResource;

    /**
     * Entity constructor.
     *
     * @param MenuSetResource $menuItem
     */
    public function __construct(MenuSetResource $menuItem)
    {
        $this->menuSetResource = $menuItem;
    }

    /**
     * @param array $data
     * @param string $key
     * @return mixed|null
     */
    public function getData(array $data, $key)
    {
        return $data[$key] ?? null;
    }
}
