<?php

namespace Ewave\Collect\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Abstract Action
 * @package Ewave\Collect\Controller
 */
abstract class AbstractAction extends Action
{
    /**
     * Collect Helper
     * 
     * @var \Ewave\Collect\Helper\Data
     */
    protected $_collectHelper;

    /**
     * StorageHandler
     *
     * @var \Ewave\Collect\Model\StorageHandler
     */
    protected $_storageHandler;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * AbstractAction constructor.
     * 
     * @param Context $context
     * @param \Ewave\Collect\Helper\Data $collectHelper
     * @param \Ewave\Collect\Model\StorageHandler $storageHandler
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Ewave\Collect\Helper\Data $collectHelper,
        \Ewave\Collect\Model\StorageHandler $storageHandler,
        \Psr\Log\LoggerInterface $logger
    ) {

        parent::__construct($context);
        $this->_collectHelper = $collectHelper;
        $this->_storageHandler = $storageHandler;
        $this->_logger = $logger;
    }
}
