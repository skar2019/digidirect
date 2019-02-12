<?php
namespace Ewave\CmsUpgrade\Model;

/**
 * Class Generator
 * @package Ewave\CmsUpgrade\Model
 */
class GeneratorContext implements \Magento\Framework\ObjectManager\ContextInterface
{
    /**
     * @var \Ewave\CmsUpgrade\Helper\Data
     */
    protected $_helper = null;

    /**
     * @var null|\Psr\Log\LoggerInterface
     */
    protected $_logger = null;

    /**
     * @var GeneratorInterface
     */
    protected $_generateEntity = null;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_filesystemDriver = null;

    /**
     * @var null|\Ewave\CmsUpgrade\Model\DataVersion
     */
    protected $_dataVersion = null;

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * GeneratorContext constructor.
     * @param \Ewave\CmsUpgrade\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param DataVersion $dataVersion
     */
    public function __construct(
        \Ewave\CmsUpgrade\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Ewave\CmsUpgrade\Model\DataVersion $dataVersion
    ) {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_filesystemDriver = $filesystemDriver;
        $this->_eventManager = $eventManager;
        $this->_dataVersion = $dataVersion;
    }

    /**
     * @return \Ewave\CmsUpgrade\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getFileSystemDriver()
    {
        return $this->_filesystemDriver;
    }

    /**
     * @return \Magento\Framework\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return DataVersion
     */
    public function getDataVersion()
    {
        return $this->_dataVersion;
    }
}
