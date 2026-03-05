<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Utils\Http\Response\Entity;

use Amasty\Base\Model\SimpleDataObject;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\DataObjectHelper;

class Converter
{
    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        ObjectFactory $objectFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->objectFactory = $objectFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param mixed $row
     * @param Config $entityConfig
     * @return SimpleDataObject
     */
    public function convertToObject($row, Config $entityConfig): SimpleDataObject
    {
        if ($entityConfig->getDataProcessor()) {
            $row = $entityConfig->getDataProcessor()->process($row);
        }

        $object = $this->objectFactory->create($entityConfig->getClassName(), []);
        $this->dataObjectHelper->populateWithArray(
            $object,
            $row,
            $entityConfig->getClassName()
        );

        return $object;
    }
}
