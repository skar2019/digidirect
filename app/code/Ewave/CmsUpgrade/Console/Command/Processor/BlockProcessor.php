<?php
namespace Ewave\CmsUpgrade\Console\Command\Processor;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Ewave\CmsUpgrade\Model\Cms\Block;
use Magento\Cms\Model\BlockFactory;

/**
 * Class BlockProcessor
 * @package Ewave\CmsUpgrade\Setup\Processor
 */
class BlockProcessor extends CmsProcessor
{
    /**
     * Init
     *
     * @param BlockFactory $modelFactory
     */
    public function __construct(BlockFactory $modelFactory)
    {
        $this->_modelFactory = $modelFactory;
    }
}
