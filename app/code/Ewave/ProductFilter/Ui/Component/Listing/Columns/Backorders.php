<?php

namespace Ewave\ProductFilter\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\CatalogInventory\Model\Source\Backorders as BackordersOption;
use Ewave\ProductFilter\Model\CollectionManager;
use Magento\CatalogInventory\Model\Configuration as CatalogInventoryConfiguration;

/**
 * Class Backorders
 * @package Ewave\ProductFilter\Ui\Component\Listing\Columns
 */
class Backorders extends Column
{
    /**
     * @var BackordersOption
     */
    private $backordersOptions;
    /**
     * @var CatalogInventoryConfiguration
     */
    private $inventoryConfiguration;

    /**
     * Backorders constructor.
     * @param BackordersOption $backordersOptions
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CatalogInventoryConfiguration $inventoryConfiguration
     * @param array $components
     * @param array $data
     */
    public function __construct(
        BackordersOption $backordersOptions,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CatalogInventoryConfiguration $inventoryConfiguration,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->backordersOptions = $backordersOptions;
        $this->inventoryConfiguration = $inventoryConfiguration;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getName();
            list($isManageStockUsed, $defaultConfigBackordersOption) = $this->getConfigCatalogInventory();
            if ($isManageStockUsed) {
                foreach ($dataSource['data']['items'] as & $item) {
                    if (key_exists($fieldName, $item)) {
                        $isConfigSettingUse = $this->isUseConfigSetting($item);
                        $item[$fieldName] =
                            $this->getTitle($item[$fieldName], $isConfigSettingUse, $defaultConfigBackordersOption);
                    }
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param string $fieldValue
     * @param string $isConfigUse
     * @param string $configValue
     * @return string
     */
    public function getTitle($fieldValue, $isConfigUse, $configValue)
    {
        $currentFieldValue = $isConfigUse ? $configValue : $fieldValue;
        $backOrdersOptions = $this->backordersOptions->toOptionArray();
        return array_reduce($backOrdersOptions, function ($acc, $value) use ($currentFieldValue) {
            if ($value['value'] == $currentFieldValue) {
                $acc = $value['label'];
            }
            return $acc;
        }, '');
    }
    /**
     * @return array;
     */
    public function getConfigCatalogInventory()
    {
        $config = [];
        $config[] = $this->inventoryConfiguration->getManageStock();
        $config[] = $this->inventoryConfiguration->getBackorders();
        return $config;
    }

    /**
     * @param array $item
     * @return bool
     */
    public function isUseConfigSetting($item)
    {
        return isset($item[CollectionManager::USE_CONFIG_BACKORDERS])
            && $item[CollectionManager::USE_CONFIG_BACKORDERS] === CollectionManager::DEFAULT_CONFIG_USE;
    }
}
