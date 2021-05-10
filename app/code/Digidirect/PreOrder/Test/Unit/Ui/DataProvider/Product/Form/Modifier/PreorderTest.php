<?php
namespace Digidirect\PreOrder\Test\Unit\Ui\DataProvider\Product\Form\Modifier;

use Digidirect\PreOrder\Test\Unit\PreOrderTestUnitTrait;
use Digidirect\PreOrder\Ui\DataProvider\Product\Form\Modifier\Preorder;
use Magento\Catalog\Model\Locator\RegistryLocator;
use Magento\Catalog\Model\Product;
use Digidirect\PreOrder\Api\Data\ProductAttributeInterface;

class PreorderTest extends \PHPUnit_Framework_TestCase
{
    use PreOrderTestUnitTrait;

    /**
     * @param $data
     * @param $expected
     * @dataProvider provideModifyData
     */
    public function testModifyData($data, $expected)
    {
        $registryLocatorMock = $this->getMockObjectWithoutConstructor(
            RegistryLocator::class,
            ['getProduct']
        );
        $productMock = $this->getMockObjectWithoutConstructor(
            Product::class,
            [
                'getId',
                'getDigidirectPreorderNote',
                'getDigidirectPreorderCartLabel'
            ]
        );
        $preorderMock = $this->getMockObjectWithConstructor(
            Preorder::class,
            ['modifyMeta'],
            [$registryLocatorMock]
        );
        $registryLocatorMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);
        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn($data['product_id']);
        $productMock->expects($this->once())
            ->method('getDigidirectPreorderNote')
            ->willReturn($data['product_note']);
        $productMock->expects($this->once())
            ->method('getDigidirectPreorderCartLabel')
            ->willReturn($data['cart_label']);

        $result = $preorderMock->modifyData([]);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function provideModifyData()
    {
        $data1 = [
            'product_id' => 1,
            'product_note' => 'Product Note',
            'cart_label' => 'Pre Order'
        ];
        $data2 = [
            'product_id' => 2,
            'product_note' => null,
            'cart_label' => null
        ];
        $expected1 = [
            1 => [
                Preorder::DATA_SOURCE_DEFAULT => [
                    ProductAttributeInterface::CODE_PREORDER_NOTE => 'Product Note',
                    ProductAttributeInterface::CODE_PREORDER_CART_LABEL => 'Pre Order'
                ]
            ]
        ];
        $expected2 = [
            2 => [
                Preorder::DATA_SOURCE_DEFAULT => [
                    ProductAttributeInterface::CODE_PREORDER_NOTE => null,
                    ProductAttributeInterface::CODE_PREORDER_CART_LABEL => null
                ]
            ]
        ];
        return [
            [$data1, $expected1],
            [$data2, $expected2]
        ];
    }
}
