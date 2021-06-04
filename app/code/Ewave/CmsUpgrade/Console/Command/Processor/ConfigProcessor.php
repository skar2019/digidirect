<?php
namespace Ewave\CmsUpgrade\Console\Command\Processor;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class ConfigProcessor
 * @package Ewave\CmsUpgrade\Setup\Processor
 */
class ConfigProcessor extends AbstractProcessor
{
    /**
     * Init
     *
     * @param \Magento\Config\Model\Config\Factory $configFactory
     */
    public function __construct(
        \Magento\Config\Model\Config\Factory $configFactory
    )
    {
        $this->_configFactory = $configFactory;
    }

    /**
     * @param array $data
     * @return \Magento\Config\Model\Config
     */
    protected function _prepareModel(array $data)
    {
        return $this->_configFactory->create(['data' => $data]);
    }
}
