<?php

namespace Ewave\Banner\Setup;

use Ewave\Banner\Model\Cache;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\App\Cache\Manager;

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
     * @var Manager
     */
    protected $cacheManager;

    /**
     * UpgradeData constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->cacheManager = $manager;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->context = $context;
        $this->processUpgrade();
    }

    /**
     * @return void
     */
    protected function processUpgrade()
    {
        foreach ($this->getVersionsCallbacks() as $version => $callback) {
            if ($this->compareVersions($version)) {
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
            '1.0.4' => 'enableBannersCache'
        ];
    }

    /**
     * @return void
     */
    protected function enableBannersCache()
    {
        $this->cacheManager->setEnabled([Cache::TYPE_IDENTIFIER], true);
    }

    /**
     * Compare versions
     *
     * @param string $new
     * @return bool
     */
    protected function compareVersions($new)
    {
        return version_compare($this->context->getVersion(), $new, '<');
    }
}
