<?php

namespace Ewave\Navigation\Model\Config\Source;

use Ewave\Navigation\Model\TypeIdMapper;
use Ewave\Navigation\Model\NotFilteredTypes;
use Magento\Framework\Data\OptionSourceInterface;

class Type implements OptionSourceInterface
{
    /**
     * @var \Ewave\Navigation\Model\ResourceModel\Type\Collection
     */
    protected $typeFactory;

    /**
     * @var TypeIdMapper
     */
    protected $typeIdMapper;

    /**
     * Type constructor.
     * @param NotFilteredTypes $notFilteredTypes
     * @param TypeIdMapper $typeIdMapper
     */
    public function __construct(
        NotFilteredTypes $notFilteredTypes,
        TypeIdMapper $typeIdMapper
    ) {
        $this->typeIdMapper = $typeIdMapper;
        $this->typeFactory = $notFilteredTypes->getCollection();
    }

    /**
     * Get array type_id => label
     *
     * @return []
     */
    public function toOptionArray()
    {
        $typesArray = $this->typeFactory->getTypesArray();
        foreach ($typesArray as $arrayKey => $type) {
            $typesArray[$arrayKey]['value'] = $this->typeIdMapper->getExpectedIdByDbId($type['value']);
        }
        return $typesArray;
    }

    /**
     * Get array type_id => type code
     *
     * @return []
     */
    public function getTypeIdCodeMapping()
    {
        $collection = $this->typeFactory;
        $result = [];
        foreach ($collection as $item) {
            $result[$item->getId()] = $item->getMenuTypeCode();

        }
        return $result;
    }
}
