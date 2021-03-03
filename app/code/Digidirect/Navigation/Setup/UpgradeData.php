<?php

namespace Digidirect\Navigation\Setup;

use Digidirect\Navigation\Model\MenuCache;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class UpgradeData
 * @package Digidirect\Navigation\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ModuleContextInterface
     */
    protected $context;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->context = $context;
        $this->setup = $setup;
        $this->setup->startSetup();
        if ($this->compareVersion('1.0.5')) {
            $this->populateRelationTable();
        }
        $this->processUpgrade();
        $this->setup->endSetup();
    }


    /**
     * @return void
     */
    protected function processUpgrade()
    {
        foreach ($this->getVersionsCallbacks() as $version => $callback) {
            if ($this->compareVersion($version)) {
                if (is_callable($callback)) {
                    call_user_func($callback);
                } elseif (is_string($callback)) {
                    $this->$callback();
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getVersionsCallbacks()
    {
        return [
            '1.0.6' => 'enableMenuCache'
        ];
    }

    /**
     * Enable menu caching
     *
     * @return void
     */
    protected function enableMenuCache()
    {
        try {
            /**
             * @var $cacheManager Manager
             */
            $cacheManager = ObjectManager::getInstance()->get(Manager::class);
            $cacheManager->setEnabled([MenuCache::TYPE_IDENTIFIER], true);
        } catch (\Throwable $exception) {
            //Cache will not be enabled
            return;
        }
    }

    /**
     * Populate relation store - menu_item table with 0 - Default Store
     *
     * @return void
     */
    protected function populateRelationTable()
    {
        $connection = $this->setup->getConnection();
        $select = $connection->select()
            ->from($this->setup->getTable('digidirect_navigation_menu_entity'), ['entity_id']);
        $menuIds = $connection->fetchCol($select);

        if (!empty($menuIds)) {
            $menuInfo = [];
            foreach ($menuIds as $menuId) {
                $menuInfo[] = [
                    'menu_entity_id' => $menuId,
                    'menu_store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                ];
            }
            $connection->insertMultiple(
                $this->setup->getTable('digidirect_navigation_menu_item_store_relation'),
                $menuInfo
            );
        }
    }

    /**
     * @param string $version
     * @return bool
     */
    protected function compareVersion(string $version)
    {
        return $this->context->getVersion() && (version_compare($this->context->getVersion(), $version) < 0);
    }
}
