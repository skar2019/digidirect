<?php

namespace Ewave\CmsUpgrade\Model;

use Ewave\CmsUpgrade\Model\Cms\Block;
use Ewave\CmsUpgrade\Model\Cms\Page;
use Ewave\CmsUpgrade\Model\Entity\Config;

/**
 * Class SetupProcessorFactory
 * @package Ewave\CmsUpgrade\Model
 */
class SetupProcessorFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * SetupFactory constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $type
     * @return bool|mixed
     */
    public function makeProcessor($type)
    {
        return $this->_objectManager->get(
            'Ewave\CmsUpgrade\Console\Command\Processor\\' . ucfirst($type) . 'Processor'
        );
    }
}
