<?php
namespace Ewave\Navigation\Test\Unit\Controller\Adminhtml\Set;

use Ewave\Navigation\Test\Unit\NavigationTestUnitTrait;

/**
 * Class SaveTest
 * @package Ewave\Navigation\Test\Unit\Controller\Adminhtml\Set
 */
class SaveTest extends \PHPUnit_Framework_TestCase
{
    use NavigationTestUnitTrait;

    /**
     * Test init set
     *
     * @dataProvider provideSetData
     * @param [] $data
     * @param [] $expected
     */
    public function testInitSet($data, $expected)
    {
        $requestMock = $this->getMockObjectWithoutConstructor('Magento\Framework\App\Request\Http', ['getParam']);
        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('set_id')
            ->willReturn($data['request_data']['set_id']);

        $context = $this->getMockObjectWithoutConstructor('Magento\Backend\App\Action\Context', ['getRequest']);
        $context->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $registry = $this->_getRegistryMockWithoutConstructor();
        $dataPersistor = $this->getMockObjectWithoutConstructor('Magento\Framework\App\Request\DataPersistor');

        $setFactory = $this->getMockObjectWithoutConstructor('Ewave\Navigation\Model\SetFactory', ['create']);

        $set = $this->getMockObjectWithoutConstructor('Ewave\Navigation\Model\Set', ['getId']);

        $setRepository = $this->getMockObjectWithoutConstructor('Ewave\Navigation\Model\SetRepository', ['getById']);

        $callRepositoryCount = $data['call_repository'];
        $callFactoryCount = $data['call_factory'];

        $setRepository->expects($this->$callRepositoryCount())
            ->method('getById')
            ->willReturn($set);

        $setFactory->expects($this->$callFactoryCount())
            ->method('create')
            ->willReturn($set);

        $set->expects($this->any())
            ->method('getId')
            ->willReturn($data['request_data']['set_id']);

        $reflection = $this->_createReflectionClass('Ewave\Navigation\Controller\Adminhtml\Set\Save');
        $controller = $reflection->newInstanceArgs([
            $context,
            $registry,
            $dataPersistor,
            $setFactory,
            $setRepository
        ]);

        $method = $reflection->getMethod('_initSet');
        $method->setAccessible(true);
        $result = $method->invokeArgs($controller, [$data['set_data']]);
        $this->assertEquals($expected, $result->getData());
    }

    /**
     * Provide data
     *
     * @return array
     */
    public function provideSetData()
    {
        $expected1 = [
            'code' => 'header',
            'name' => 'Header Set',
            'set_id' => 12
        ];
        $data1 = [
            'set_data' => [
                'code' => 'header',
                'name' => 'Header Set',
                'set_id' => 12
            ],
            'request_data' => [
                'set_id' => 150
            ],
            'call_factory' => 'never',
            'call_repository' => 'once'
        ];

        $expected2 = [
            'code' => 'header',
            'name' => 'Header Set',
            'set_id' => null
        ];
        $data2 = [
            'set_data' => [
                'code' => 'header',
                'name' => 'Header Set',
                'set_id' => null
            ],
            'request_data' => [
                'set_id' => null
            ],
            'call_factory' => 'once',
            'call_repository' => 'never'
        ];

        return [
            [$data1, $expected1],
            [$data2, $expected2]
        ];
    }
}
