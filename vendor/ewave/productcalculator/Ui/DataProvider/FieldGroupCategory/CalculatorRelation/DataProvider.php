<?php

namespace Ewave\ProductCalculator\Ui\DataProvider\FieldGroupCategory\CalculatorRelation;

use Ewave\ProductCalculator\Model\ResourceModel\FieldGroupCategory\CollectionFactory;

/**
 * Class DataProvider
 * @package Ewave\ProductCalculator\Ui\DataProvider\FieldGroupCategory\CalculatorRelation
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
    }
}
