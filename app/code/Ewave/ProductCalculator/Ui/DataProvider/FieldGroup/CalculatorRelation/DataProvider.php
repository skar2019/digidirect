<?php
namespace Ewave\ProductCalculator\Ui\DataProvider\FieldGroup\CalculatorRelation;

use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroup\CollectionFactory;

/**
 * Class DataProvider
 * @package Ewave\ProductCalculator\Ui\DataProvider\FieldGroup\CalculatorRelation
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->collection->addFieldToFilter(FieldGroupInterface::CATEGORY_ID, ['null' => true]);
    }
}
