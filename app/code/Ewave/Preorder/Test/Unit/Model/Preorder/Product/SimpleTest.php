<?php
namespace Ewave\PreOrder\Test\Unit\Model\Preorder\Product;

use Ewave\PreOrder\Test\Unit\PreOrderTestUnitTrait;
use Ewave\PreOrder\Model\Preorder\Product\Simple;
use Ewave\PreOrder\Helper\Data as PreOrderHelper;
use Ewave\PreOrder\Helper\Config;
use Magento\CatalogInventory\Model\Stock\Item as StockItem;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\Catalog\Model\Product;

/**
 * Class SimpleTest
 * @package Ewave\PreOrder\Test\Unit\Model\Preorder\Product
 */
class SimpleTest extends \PHPUnit_Framework_TestCase
{
    use PreOrderTestUnitTrait;

    /**
     * @param $data
     * @param $expected
     * @dataProvider provideProductPreorderData
     */
    public function testIsProductPreorder($data, $expected)
    {
        $helperMock = $this->getMockObjectWithoutConstructor(
            PreOrderHelper::class,
            ['getConfig']
        );
        $stockRegistryMock = $this->getMockObjectWithoutConstructor(
            StockRegistry::class,
            ['getStockItem']
        );
        $stockItemMock = $this->getMockObjectWithoutConstructor(
            StockItem::class,
            [
                'getQty',
                'getMinQty',
                'getIsInStock',
                'getBackorders'
            ]
        );
        $configMock = $this->getMockObjectWithoutConstructor(
            Config::class,
            ['disableForPositiveQty']
        );
        $simpleModelMock = $this->getMockObjectWithConstructor(
            Simple::class,
            null,
            [
                $helperMock,
                $stockRegistryMock
            ]
        );
        $productMock = $this->getMockObjectWithoutConstructor(
            Product::class,
            ['getId']
        );
        $stockRegistryMock->expects($this->any())
            ->method('getStockItem')
            ->willReturn($stockItemMock);
        $helperMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($configMock);
        $stockItemMock->expects($this->any())
            ->method('getBackorders')
            ->willReturn(PreOrderHelper::BACKORDERS_PREORDER_OPTION);
        $stockItemMock->expects($this->any())
            ->method('getQty')
            ->willReturn($data['qty']);
        $configMock->expects($this->any())
            ->method('disableForPositiveQty')
            ->willReturn($data['disable_for_positive_qty']);

        $result = $simpleModelMock->isProductPreorder($productMock, $data['required_qty']);

        $this->assertEquals($result, $expected);
    }

    /**
     * @return array
     */
    public function provideProductPreorderData()
    {
        $data1 = [
            'qty' => 7,
            'required_qty' => 1,
            'disable_for_positive_qty' => true
        ];
        $data2 = [
            'qty' => 1,
            'required_qty' => 7,
            'disable_for_positive_qty' => true
        ];
        $data3 = [
            'qty' => 1,
            'required_qty' => 7,
            'disable_for_positive_qty' => false
        ];
        return [
            [$data1, false],
            [$data2, true],
            [$data3, true]
        ];
    }
}
