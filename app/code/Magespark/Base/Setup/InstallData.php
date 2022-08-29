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

namespace MageSpark\Base\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\State;
use Psr\Log\LoggerInterface;
use MageSpark\Base\Helper\Module;
use Magento\Framework\App\Area;

class InstallData implements InstallDataInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * UpgradeData constructor.
     *
     * @param State $appState
     * @param LoggerInterface $logger
     * @param Module $moduleHelper
     */
    public function __construct(
        State $appState,
        LoggerInterface $logger,
        Module $moduleHelper
    ) {
        $this->logger = $logger;
        $this->appState = $appState;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->appState->emulateAreaCode(
            Area::AREA_ADMINHTML,
            [$this, 'installCallback'],
            [$setup, $context]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function installCallback(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        try {
            $this->moduleHelper->reload();
        } catch (\Exception $ex) {
            $this->logger->critical($ex);
        }

        $setup->endSetup();
    }
}
