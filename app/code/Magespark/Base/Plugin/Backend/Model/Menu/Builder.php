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


namespace MageSpark\Base\Plugin\Backend\Model\Menu;

use Magento\Backend\Model\Menu;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Builder
 * @package MageSpark\Base\Plugin\Backend\Model\Menu
 */
class Builder
{
    const BASE_MENU = 'MageSpark_Base::menu';

    const SEO_PARAMS = '?utm_source=extension&utm_medium=backend&utm_campaign=common_menu_to_guide';

    const SETTING_ENABLE = 'magespark_base/menu/enable';

    /**
     * @var Menu\Config
     */
    private $menuConfig;

    /**
     * @var array|null
     */
    private $magesparkItems = null;

    /**
     * @var Menu\Filter\IteratorFactory
     */
    private $iteratorFactory;

    /**
     * @var \Magento\Backend\Model\Menu\ItemFactory
     */
    private $itemFactory;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @var \MageSpark\Base\Helper\Module
     */
    private $moduleHelper;

    /**
     * @var \Magento\Config\Model\Config\Structure
     */
    private $configStructure;

    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $metadata;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Backend\Model\Menu\Config $menuConfig,
        \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory,
        \Magento\Backend\Model\Menu\ItemFactory $itemFactory,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \MageSpark\Base\Helper\Module $moduleHelper,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Framework\App\ProductMetadataInterface $metadata,
        ObjectFactory $objectFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->menuConfig = $menuConfig;
        $this->iteratorFactory = $iteratorFactory;
        $this->itemFactory = $itemFactory;
        $this->moduleList = $moduleList;
        $this->moduleHelper = $moduleHelper;
        $this->configStructure = $configStructure;
        $this->objectFactory = $objectFactory;
        $this->metadata = $metadata;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $subject
     * @param Menu $menu
     *
     * @return Menu
     */
    public function afterGetResult($subject, Menu $menu)
    {
        try {
            $menu = $this->observeMenu($menu);
        } catch (\Exception $ex) {
            $menu->critical($ex->getMessage());
        }

        return $menu;
    }

    /**
     * @param Menu $menu
     * @return Menu
     * @throws \Exception
     */
    private function observeMenu(Menu $menu)
    {
        $item = $menu->get(self::BASE_MENU);

        if (!$item) {
            return $menu;
        }

        if (version_compare($this->metadata->getVersion(), '2.2.0', '<')

        ) {
            $menu->remove(self::BASE_MENU);
            return $menu;
        }

        $origMenu = $this->menuConfig->getMenu();
        $menuItems = $this->getMenuItems($origMenu);
        $configItems = $this->getConfigItems();

        foreach ($this->getInstalledModules($configItems) as $title => $installedModule) {
            $itemsToAdd = [];
            $moduleInfo = $this->moduleHelper->getFeedModuleData($installedModule);

            if (isset($menuItems[$installedModule])) {
                $this->cloneMenuItems($menuItems[$installedModule], $itemsToAdd, $menu);
            }

            if (isset($configItems[$installedModule]['id'])) {
                $magesparkItem = $this->generateMenuItem(
                    $installedModule . '::menuconfig',
                    $installedModule,
                    self::BASE_MENU,
                    'adminhtml/system_config/edit/section/' . $configItems[$installedModule]['id'],
                    __('Configuration')->render()
                );
                $itemsToAdd[] = $magesparkItem;
            }

            if (isset($moduleInfo['guide']) && $moduleInfo['guide']) {
                $magesparkItem = $this->generateMenuItem(
                    $installedModule . '::menuguide',
                    $installedModule,
                    self::BASE_MENU,
                    'adminhtml/system_config/edit/section/msbase',
                    __('User Guide')->render()
                );
                $itemsToAdd[] = $magesparkItem;
            }

            if ($itemsToAdd) {
                $itemId = $installedModule . '::container';
                /** @var \Magento\Backend\Model\Menu\Item $module */
                $module = $this->itemFactory->create(
                    [
                        'data' => [
                            'id'       => $itemId,
                            'title'    => $title,
                            'module'   => $installedModule,
                            'resource' => self::BASE_MENU
                        ]
                    ]
                );
                $menu->add($module, self::BASE_MENU, 1);
                foreach ($itemsToAdd as $copy) {
                    if ($copy) {
                        $menu->add($copy, $itemId, null);
                    }
                }
            }
        }

        return $menu;
    }

    /**
     * Add data into menu item array
     *
     * @param array $menuItems
     * @param array $itemsToAdd
     * @param Menu $menu
     */
    private function cloneMenuItems($menuItems, &$itemsToAdd, Menu $menu)
    {
        foreach ($menuItems as $link) {
            $magesparkItem = $menu->get($link);
            if ($magesparkItem) {
                $itemData = $magesparkItem->toArray();
                if (isset($itemData['id'])
                    && isset($itemData['resource'])
                    && isset($itemData['action'])
                    && isset($itemData['title'])
                ) {
                    $module = isset($itemData['module']) ? $itemData['module'] : explode('::', $itemData['resource'])[0];
                    $itemsToAdd[] = $this->generateMenuItem(
                        $itemData['id'] . 'menu',
                        $module,
                        $itemData['resource'],
                        $itemData['action'],
                        $itemData['title']
                    );
                }
            }
        }
    }

    /**
     * Creating the menu items
     *
     * @param $id
     * @param $installedModule
     * @param $resource
     * @param $url
     * @param $title
     *
     * @return bool|Menu\Item
     */
    private function generateMenuItem($id, $installedModule, $resource, $url, $title)
    {
        try {
            $item = $this->itemFactory->create(
                [
                    'data' => [
                        'id'           => $id,
                        'title'        => $title,
                        'module'       => $installedModule,
                        'action'       => $url,
                        'resource'     => $resource
                    ]
                ]
            );
        } catch (\Exception $ex) {
            $item = false;
        }

        return $item;
    }

    /**
     * Get all installed module
     *
     * @param $configItems
     *
     * @return array
     */
    private function getInstalledModules($configItems)
    {
        $installed = [];
        $modules = $this->moduleList->getNames();
        $dispatchResult = $this->objectFactory->create(['data' => $modules]);
        $modules = $dispatchResult->toArray();

        foreach ($modules as $moduleName) {
            if (strstr($moduleName, 'MageSpark_') === false
                || $moduleName === 'MageSpark_Base'
                || in_array($moduleName, $this->moduleHelper->getRestrictedModules())
            ) {
                continue;
            }

            $title = (isset($configItems[$moduleName]['label']) && $configItems[$moduleName]['label'])
                ? $configItems[$moduleName]['label']
                : $this->getModuleTitle($moduleName);

            $installed[$title] = $moduleName;
        }
        ksort($installed);

        return $installed;
    }

    /**
     * Store all menu items into item array
     *
     * @param Menu $menu
     *
     * @return array|null
     */
    private function getMenuItems(Menu $menu)
    {
        if ($this->magesparkItems === null) {
            $all = $this->generateMageSparkItems($menu);
            $this->magesparkItems = [];
            foreach ($all as $item) {
                $name = explode('::', $item);
                $name = $name[0];
                if (!isset($this->magesparkItems[$name])) {
                    $this->magesparkItems[$name] = [];
                }
                $this->magesparkItems[$name][] = $item;
            }
        }

        return $this->magesparkItems;
    }

    /**
     * Return all config items
     *
     * @return array
     */
    private function getConfigItems()
    {
        $configItems = [];
        $config = $this->generateConfigItems();
        foreach ($config as $item => $section) {
            $name = explode('::', $item);
            $name = $name[0];
            if (!isset($configItems[$name])) {
                $configItems[$name] = [];
            }
            $configItems[$name] = $section;
        }

        return $configItems;
    }

    /**
     * @return array
     */
    private function generateMageSparkItems($menu)
    {
        $magespark = [];
        foreach ($this->getMenuIterator($menu) as $menuItem) {
            $menuId = $menuItem->getId();
            if (strpos($menuId, 'MageSpark') !== false
                && strpos($menuId, 'MageSpark_Base') === false
                && ($menuItem->getAction() && strpos($menuItem->getAction(), 'system_config') === false)
            ) {
                $magespark[] = $menuId;
            }
            if ($menuItem->hasChildren()) {
                $magespark = array_merge($magespark, $this->generateMageSparkItems($menuItem->getChildren()));
            }
        }

        return $magespark;
    }

    /**
     * Get menu filter iterator
     *
     * @param \Magento\Backend\Model\Menu $menu
     *
     * @return \Magento\Backend\Model\Menu\Filter\Iterator
     */
    private function getMenuIterator($menu)
    {
        return $this->iteratorFactory->create(['iterator' => $menu->getIterator()]);
    }

    /**
     * Get current module title
     *
     * @param $name
     *
     * @return string
     */
    private function getModuleTitle($name)
    {
        $result = $name;
        $module = $this->moduleHelper->getFeedModuleData($name);
        if ($module && isset($module['name'])) {
            $result = $module['name'];
            $result = str_replace(' for Magento 2', '', $result);
        } else {
            $result = str_replace('MageSpark_', '', $result);
            preg_match_all('/((?:^|[A-Z])[a-z]+)/', $result, $matches);
            if (isset($matches[1]) && $matches[1]) {
                $result = implode(' ', $matches[1]);
            }
        }

        return $result;
    }

    /**
     * Generate configuration item and store in array
     *
     * @return array
     */
    private function generateConfigItems()
    {
        $result = [];
        $configTabs = $this->configStructure->getTabs();
        $config = $this->findResourceChildren($configTabs, 'magespark');

        if ($config) {
            foreach ($config as $item) {
                $data = $item->getData('resource');
                if (isset($data['resource'])
                    && isset($data['id'])
                    && $data['id']
                ) {
                    $result[$data['resource']] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * Find the children of the current node
     *
     * @param $config
     * @param $name
     *
     * @return array
     */
    private function findResourceChildren($config, $name)
    {
        $result = [];
        $currentNode = null;
        foreach ($config as $key => $node) {
            if ($node->getId() == $name) {
                $currentNode = $node;
                break;
            }
        }

        if ($currentNode) {
            $result = $currentNode->getChildren();
        }

        return $result;
    }
}
