<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */

namespace MageSpark\Base\Setup;

use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\DB\DataConverter\SerializedToJson;

class SerializedFieldDataConverter
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ResourceConnection
     */
    private $connectionResource;

    /**
     * SerializedFieldDataConverter constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ResourceConnection $connectionResource
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ResourceConnection $connectionResource
    ) {
        $this->objectManager = $objectManager;
        $this->connectionResource = $connectionResource;
    }

    /**
     * Convert metadata from serialized to JSON format:
     *
     * @param $tableName
     * @param $identifierField
     * @param $fields
     * @return void
     */
    public function convertSerializedDataToJson($tableName, $identifierField, $fields)
    {
        /** @var AggregatedFieldDataConverter $aggregatedFieldConverter */
        $fieldConverter = $this->objectManager->get(AggregatedFieldDataConverter::class);
        $convertData = [];

        if (is_array($fields)) {
            foreach ($fields as $field) {
                $convertData[] = $this->getConvertedData($tableName, $identifierField, $field);
            }
        } else {
            $convertData[] = $this->getConvertedData($tableName, $identifierField, $fields);
        }

        $fieldConverter->convert(
            $convertData,
            $this->connectionResource->getConnection()
        );
    }

    /**
     * Convert metadata to JSON format:
     *
     * @param $tableName
     * @param $identifierField
     * @param $field
     * @return FieldToConvert
     */
    protected function getConvertedData($tableName, $identifierField, $field)
    {
        $instance = new FieldToConvert(
            SerializedToJson::class,
            $this->connectionResource->getTableName($tableName),
            $identifierField,
            $field
        );

        return $instance;
    }
}
