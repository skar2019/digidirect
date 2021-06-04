<?php
namespace Ewave\Store\Setup;

use Ewave\Store\Setup\StoreEntitySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Ewave\Store\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Ewave\Store\Setup\StoreEntitySetupFactory
     */
    protected $_storeEntitySetupFactory;

    /**
     * InstallData constructor.
     * @param \Ewave\Store\Setup\StoreEntitySetupFactory $storeEntitySetupFactory
     */
    public function __construct(StoreEntitySetupFactory $storeEntitySetupFactory)
    {
        $this->_storeEntitySetupFactory = $storeEntitySetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $entitySetup = $this->_storeEntitySetupFactory->create(['setup' => $setup]);
        $entitySetup->installEntities();
    }
}
