<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Test\Unit\Utils\Http\Response\Entity;

use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo;
use Amasty\Base\Utils\Http\Response\Entity\Config;
use Amasty\Base\Utils\Http\Response\Entity\Converter;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\DataObjectHelper;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    private $model;

    /**
     * @var ObjectFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $objectFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dataObjectHelperMock;

    protected function setUp(): void
    {
        $this->objectFactoryMock = $this->createPartialMock(ObjectFactory::class, ['create']);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);
        $this->model = new Converter($this->objectFactoryMock, $this->dataObjectHelperMock);
    }

    public function testConvertToObject(): void
    {
        $row = [];
        $entityConfig = [
            Config::CLASS_NAME => InstanceInfo::class
        ];
        $instanceInfoMock = $this->createMock(InstanceInfo::class);
        $entityConfigMock = $this->createPartialMock(Config::class, ['getDataProcessor', 'getClassName']);
        $entityConfigMock
            ->expects($this->exactly(2))
            ->method('getClassName')
            ->willReturn($entityConfig[Config::CLASS_NAME]);

        $this->objectFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($entityConfig[Config::CLASS_NAME], [])
            ->willReturn($instanceInfoMock);
        $this->dataObjectHelperMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->with($instanceInfoMock, $row, $entityConfig[Config::CLASS_NAME])
            ->willReturn($instanceInfoMock);

        $this->assertEquals($instanceInfoMock, $this->model->convertToObject($row, $entityConfigMock));
    }
}
