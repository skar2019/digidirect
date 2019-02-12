<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem\Column;

use Ewave\NavigationCMSUpgrade\Model\Processor\ResourceModel\MenuItem as ResourceMenuItem;
use Ewave\Navigation\Model\MenuFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MenuItem
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
class MenuItem
{
    /**
     * @var MenuFactory
     */
    protected $menuItemResource;

    /**
     * @var []
     */
    private $upgradeData;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * MenuItem constructor.
     *
     * @param ResourceMenuItem $menu
     * @param MenuFactory $menuFactory
     * @param array $config
     */
    public function __construct(
        ResourceMenuItem $menu,
        MenuFactory $menuFactory,
        array $config = []
    ) {
        $this->menuItemResource = $menu;
        $this->config = $config;
        $this->menuFactory = $menuFactory;
    }

    /**
     * Upgrade menu
     *
     * @return void
     */
    public function upgrade()
    {
        foreach ($this->upgradeData as $data) {
            foreach ($data as $item) {
                if (!isset($item['menu_item_code'])) {
                    continue;
                }
                try {
                    $data = $this->getData($item);
                    /**
                     * @var $model \Ewave\Navigation\Model\Menu
                     */
                    $model = $this->menuFactory->create();
                    $model->setData($data);
                    $model->getResource()->save($model);
                } catch (LocalizedException $e) {
                    continue;
                }
            }
        }
    }

    /**
     * @param [] $itemData
     * @return array
     */
    protected function getData($itemData)
    {
        $newData = [];
        foreach ($this->config as $entityKey => $entityConfig) {
            $class = null;
            if (isset($entityConfig['class'])) {
                $class = $entityConfig['class'];
            }

            if ($this->issetAndIsArray($entityConfig, 'fields')) {
                foreach ($entityConfig['fields'] as $fieldCode => $field) {
                    $class = $this->issetAndInstanceOf($entityConfig, 'class') ? $entityConfig['class'] : $class;
                    if (!$class) {
                        continue;
                    }

                    $callback = $field['callback'] ?? null;
                    $newData[$fieldCode] = $callback ? $class->$callback($itemData)
                        : $class->getData($itemData, $fieldCode);
                }
            }
        }
        return $newData;
    }

    /**
     * @param array $array
     * @param string $key
     * @return bool
     */
    protected function issetAndIsArray(array $array, $key)
    {
        return isset($array[$key]) && is_array($array[$key]);
    }

    /**
     * @param [] $array
     * @param string $key
     * @return bool
     */
    protected function issetAndInstanceOf($array, $key)
    {
        return isset($array[$key]) && ($array[$key] instanceof FieldProcessorInterface);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setUpgradeData(array $data = [])
    {
        $this->upgradeData = $data;
        return $this;
    }
}
