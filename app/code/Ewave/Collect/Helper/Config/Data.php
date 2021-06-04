<?php

namespace Ewave\Collect\Helper\Config;

use Ewave\Collect\Model\SourceProcessor;
use Magento\Framework\Webapi\Exception;
use Ewave\Collect\Api\CollectPlaceStockInterface;

/**
 * Class Data
 *
 * @package Ewave\Collect\Helper\Config
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Ewave\Collect\Model\Config\Data
     */
    protected $_collectPlaceConfig;

    /**
     * @var SourceProcessor
     */
    protected $_sourceProcessor;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Ewave\Collect\Model\Config\Data $collectPlaceConfig
     * @param SourceProcessor $sourceProcessor
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Ewave\Collect\Model\Config\Data $collectPlaceConfig,
        \Ewave\Collect\Model\SourceProcessor $sourceProcessor
    ) {
        parent::__construct($context);

        $this->_collectPlaceConfig = $collectPlaceConfig;
        $this->_sourceProcessor = $sourceProcessor;
    }

    /**
     * GetSourceByStorage
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return \Ewave\Collect\Api\CollectPlaceStockInterface|bool
     * @throws Exception
     * @throws \Zend_Xml_Exception
     */
    public function getSourceByStorage($quoteItem)
    {
        $source = false;
        if ($storageName = $quoteItem->getCollectPlaceStorageName()) {
            $storageConfig = $this->_collectPlaceConfig->getStorageForHandle($storageName);
            if ($storageConfig && isset($storageConfig['source'])) {
                $source = $this->_sourceProcessor->getProcessor($storageConfig['source']);
            }
        }

        return $source;
    }
}
