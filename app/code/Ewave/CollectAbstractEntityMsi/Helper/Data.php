<?php
namespace Ewave\CollectAbstractEntityMSI\Helper;

use Ewave\CollectAbstractEntityMSI\Api\Data\CollectFields\Constants;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Ewave\CollectAbstractEntityMSI\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Ewave\CollectAbstractEntity\Helper\Config
     */
    protected $configHelper;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Ewave\CollectAbstractEntity\Helper\Config $configHelper
     */
    public function __construct(
        Context $context,
        \Ewave\CollectAbstractEntity\Helper\Config $configHelper
    ) {
        parent::__construct($context);
        $this->configHelper = $configHelper;
    }

    /**
     * Get MSI Inventory Source attribute code from Abstract Entity, selected in configuration matrix.
     * @return string|bool
     */
    public function getSourceInventoryAttributeFromConfig()
    {
        $matrix = $this->configHelper->getCollectFieldsMatrix();
        return $matrix[Constants::COLLECT_FIELD_INVENTORY_SOURCE] ?? false;
    }
}
