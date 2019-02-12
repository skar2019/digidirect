<?php

namespace Ewave\CheckoutFields\Helper\Xml\Fields;

use \Ewave\CheckoutFields\Helper\Config;
use Ewave\CheckoutFields\Model\Config\Data as FieldsConfig;
use \Magento\Framework\Module\Dir\Reader;
use \Magento\Framework\Xml\Parser as MagentoParser;
use \Ewave\CheckoutFields\Model\Condition\ConditionInterface;

/**
 * Class Parser
 * @package Ewave\CheckoutFields\Helper\Xml\Fields
 */
class Parser
{
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleDirReader;

    /**
     * @var MagentoParser
     */
    protected $fieldsConfig;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var null|array
     */
    protected $fields = null;

    /**
     * @var array
     */
    protected $conditions;

    /**
     * Parser constructor.
     * @param FieldsConfig $fieldsConfig
     * @param Config $config
     * @param array $conditions
     */
    public function __construct(FieldsConfig $fieldsConfig, Config $config, array $conditions = [])
    {
        $this->config = $config;
        $this->fieldsConfig = $fieldsConfig;
        $this->conditions = $conditions;
    }

    /**
     * @param string|null $scopeId
     * @param bool $isPdpFields
     * @return array|null
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getFields($scopeId = null, $isPdpFields = false)
    {
        if (!$this->config->isEnabled()) {
            return [];
        }
        if (null === $this->fields) {
            $activeFields = $isPdpFields ?
                $this->config->getActivePDPFields($scopeId) : $this->config->getActiveCheckoutFields($scopeId);
            $allFields = $this->getAllFields();

            foreach ($allFields as $code => $field) {
                if (!(isset($activeFields[$code]) && $this->isFieldActive($code, $activeFields[$code]))) {
                    unset($allFields[$code]);
                }
            }
            $this->fields = $allFields;
        }
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getAllFields()
    {
        $parsedArray = $this->fieldsConfig->getFields();
        if (isset($parsedArray['fields'])) {
            return (array)$parsedArray['fields'];
        }
        return [];
    }

    /**
     * @param string $code
     * @param array $options
     * @return bool
     */
    public function isFieldActive($code, $options = [])
    {
        foreach ($this->conditions as $condition) {
            /** @var ConditionInterface $condition */
            if (!$condition instanceof ConditionInterface) {
                continue;
            }

            if (!$condition->isValid($code, $options)) {
                return false;
            }
        }

        return true;
    }
}
