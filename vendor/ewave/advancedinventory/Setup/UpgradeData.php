<?php
namespace Ewave\AdvancedInventory\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\CatalogInventory\Model\Stock;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
            $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
            $salesInstaller->addAttribute('order', 'stock_id', [
                'type' => 'int',
                'visible' => false,
                'default' => Stock::DEFAULT_STOCK_ID
            ]);
        }
        $setup->endSetup();
    }
}
