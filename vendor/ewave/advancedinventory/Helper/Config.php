<?php
namespace Ewave\AdvancedInventory\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_GENERAL_ENABLED = 'ewave_advancedinventory/general/enable';
    const XML_PATH_GENERAL_ABSTRACT_ENTITIES = 'ewave_advancedinventory/general/abstract_entities';
    const XML_PATH_ORDER_ASSIGNMENT_RULE = 'ewave_advancedinventory/order_assignment/assignment_rule';
    const XML_PATH_ORDER_WEBSITE_STOCK = 'ewave_advancedinventory/order_assignment/website_stock';

    /**
     * @var array
     */
    protected $allowedProductTypes;

    /**
     * Config constructor.
     * @param Context $context
     * @param array $allowedProductTypes
     */
    public function __construct(
        Context $context,
        array $allowedProductTypes = []
    ) {
        parent::__construct($context);
        $this->allowedProductTypes = $allowedProductTypes;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_ENABLED
        );
    }

    /**
     * @return array
     */
    public function getAbstractEntities()
    {
        return $this->getMultiSelectConfig(
            self::XML_PATH_GENERAL_ABSTRACT_ENTITIES,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * @return string
     */
    public function getOrderAssignmentRule()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ORDER_ASSIGNMENT_RULE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @param null|string $scopeCode
     * @return string
     */
    public function getWebsiteStock($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ORDER_WEBSITE_STOCK,
            ScopeInterface::SCOPE_WEBSITE,
            $scopeCode
        );
    }

    /**
     * @param string $path
     * @param string $scopeType
     * @param string $scopeCode
     * @return array
     */
    protected function getMultiSelectConfig($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $value = $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
        if ($value) {
            return explode(',', $value);
        }
        return [];
    }

    /**
     * @return array
     */
    public function getAllowedProductTypes()
    {
        return $this->allowedProductTypes;
    }
}
