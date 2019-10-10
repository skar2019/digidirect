<?php

namespace Ewave\ProductFilter\Model;

use Magento\Framework\Data\Collection;
use Magento\CatalogInventory\Model\Configuration as CatalogInventoryConfiguration;

class CollectionManager
{
    const FIELD_NAME = 'backorders_options';
    const USE_CONFIG_BACKORDERS = 'use_config_backorders';
    const DEFAULT_CONFIG_USE = '1';
    /**
     * @var CatalogInventoryConfiguration
     */
    private $inventoryConfiguration;

    /**
     * CollectionManager constructor.
     * @param CatalogInventoryConfiguration $inventoryConfiguration
     */
    public function __construct(
        CatalogInventoryConfiguration $inventoryConfiguration
    ) {
        $this->inventoryConfiguration = $inventoryConfiguration;
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    public function preparedCollection(Collection $collection)
    {
        $columns = $collection->getSelect()->getPart(\Zend_Db_Select::COLUMNS);
        if ($this->inventoryConfiguration->getManageStock() && !$this->checkJoin($columns)) {
            $collection->joinTable(
                'cataloginventory_stock_item',
                'product_id=entity_id',
                [self::FIELD_NAME => 'backorders', self::USE_CONFIG_BACKORDERS => self::USE_CONFIG_BACKORDERS],
                ['stock_id' => '1'],
                'left'
            );
            return $collection;
        }
        return null;
    }

    /**
     * @param Collection|null $collection
     * @param string $field
     * @param array $condition
     * @return $this
     */
    public function addFilter($collection, $field, $condition)
    {
        if ($collection !== null) {
            $isDefaultConfigUseInFilter = $this->checkCondition($condition);
            if ($isDefaultConfigUseInFilter) {
                $collection->addFieldToFilter(
                    [
                        ['attribute' => 'use_config_backorders','in' => [1]],
                        ['attribute' => 'backorders_options', 'in' => $condition['in']]
                    ],
                    []
                );
                return $this;
            }
            $collection->addFieldToFilter($field, $condition);
            $collection->addFieldToFilter('use_config_backorders', ['eq' => 0]);
        }
        return $this;
    }

    /**
     * @param array|null $condition
     * @return bool
     */
    public function checkCondition($condition)
    {
        $currentConfigBackorder = $this->inventoryConfiguration->getBackorders();
        if (!empty($condition['in']) && isset($currentConfigBackorder)) {
            return in_array($currentConfigBackorder, $condition['in']);
        }
        return false;
    }

    /**
     * @param array $columns
     * @return bool
     */
    public function checkJoin($columns)
    {
        foreach ($columns as $column) {
            if (is_array($column)) {
                if (in_array(self::FIELD_NAME, $column)) {
                    return true;
                }
            }
        }
        return false;
    }
}
