<?php
namespace Digidirect\PreOrder\Test\Unit\Model;

use Digidirect\PreOrder\Test\Unit\PreOrderTestUnitTrait;
use Digidirect\PreOrder\Model\OrderItemPreorderRepository;
use Digidirect\PreOrder\Model\OrderItemPreorder;

/**
 * Class OrderItemPreorderRepositoryTest
 * @package Digidirect\PreOrder\Test\Unit\Model
 */
class OrderItemPreorderRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use PreOrderTestUnitTrait;

    /**
     * @param $data
     * @param $expected
     * @dataProvider providePreorderItemData
     */
    public function testIsOrderItemHasPreorderFlag($data, $expected)
    {
        $repositoryMock = $this->getMockObjectWithoutConstructor(
            OrderItemPreorderRepository::class,
            ['getByOrderItemId']
        );
        $orderItemsPreorderMock = $this->getMockObjectWithoutConstructor(
            OrderItemPreorder::class,
            ['getIsPreorder']
        );
        $repositoryMock->expects($this->any())
            ->method('getByOrderItemId')
            ->willReturn($orderItemsPreorderMock);
        $orderItemsPreorderMock->expects($this->once())
            ->method('getIsPreorder')
            ->willReturn($data);

        $result = $repositoryMock->isOrderItemHasPreorderFlag(123);

        $this->assertEquals($result, $expected);
    }

    /**
     * @return array
     */
    public function providePreorderItemData()
    {
        $preordered = true;
        return [
            [$preordered, $preordered],
            [!$preordered, !$preordered]
        ];
    }
}
