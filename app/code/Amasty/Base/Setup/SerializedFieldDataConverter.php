<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/


namespace Amasty\Base\Setup;

use Magento\Framework\DB\AggregatedFieldDataConverter;

class SerializedFieldDataConverter
{
    /**
     * @var AggregatedFieldDataConverter
     */
    private $fieldConverter;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $connectionResource;

    public function __construct(
        AggregatedFieldDataConverter $fieldConverter,
        \Magento\Framework\App\ResourceConnection $connectionResource
    ) {
        $this->fieldConverter = $fieldConverter;
        $this->connectionResource = $connectionResource;
    }

    /**
     * Convert metadata from serialized to JSON format:
     *
     * @param string|string[] $tableName
     * @param string          $identifierField
     * @param string|string[] $fields
     * @return void
     */
    public function convertSerializedDataToJson($tableName, $identifierField, $fields)
    {
        $convertData = [];

        if (is_array($fields)) {
            foreach ($fields as $field) {
                $convertData[] = $this->getConvertedData($tableName, $identifierField, $field);
            }
        } else {
            $convertData[] = $this->getConvertedData($tableName, $identifierField, $fields);
        }

        $this->fieldConverter->convert(
            $convertData,
            $this->connectionResource->getConnection()
        );
    }

    /**
     * @param string|string[] $tableName
     * @param string          $identifierField
     * @param string          $field
     * @return \Magento\Framework\DB\FieldToConvert
     */
    protected function getConvertedData($tableName, $identifierField, $field)
    {
        $instance = new \Magento\Framework\DB\FieldToConvert(
            \Magento\Framework\DB\DataConverter\SerializedToJson::class,
            $this->connectionResource->getTableName($tableName),
            $identifierField,
            $field
        );

        return $instance;
    }
}
