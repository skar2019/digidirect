<?php
namespace Digidirect\PreOrder\Test\Unit\Helper;

use Digidirect\PreOrder\Test\Unit\PreOrderTestUnitTrait;
use Magento\CatalogInventory\Model\Stock\Item as StockItem;
use Digidirect\PreOrder\Helper\Data;
use Digidirect\PreOrder\Helper\Config;

/**
 * Class DataTest
 * @package Digidirect\PreOrder\Test\Unit\Helper
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    use PreOrderTestUnitTrait;

    /**
     * @param $data
     * @param $expected
     * @dataProvider provideStockItemData
     */
    public function testVerifyStockItem($data, $expected)
    {
        $helperMock = $this->getMockObjectWithoutConstructor(
            Data::class,
            ['getConfig']
        );
        $configMock = $this->getMockObjectWithoutConstructor(
            Config::class,
            ['isAllowEmptyQty']
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
        $helperMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($configMock);
        $configMock->expects($this->any())
            ->method('isAllowEmptyQty')
            ->willReturn($data['is_allow_empty_qty']);
        $stockItemMock->expects($this->any())
            ->method('getQty')
            ->willReturn($data['qty']);
        $stockItemMock->expects($this->any())
            ->method('getMinQty')
            ->willReturn($data['min_qty']);
        $stockItemMock->expects($this->any())
            ->method('getIsInStock')
            ->willReturn($data['is_in_stock']);
        $stockItemMock->expects($this->any())
            ->method('getBackorders')
            ->willReturn(Data::BACKORDERS_PREORDER_OPTION);

        $result = $helperMock->verifyStockItem($stockItemMock);

        $this->assertEquals($result, $expected);
    }

    /**
     * @return array
     */
    public function provideStockItemData()
    {
        $data1 = [
            'qty' => -5,
            'min_qty' => 1,
            'is_in_stock' => true,
            'is_allow_empty_qty' => true
        ];
        $data2 = [
            'qty' => 6,
            'min_qty' => 1,
            'is_in_stock' => true,
            'is_allow_empty_qty' => true
        ];
        $data3 = [
            'qty' => 6,
            'min_qty' => 1,
            'is_in_stock' => false,
            'is_allow_empty_qty' => true
        ];
        $data4 = [
            'qty' => 6,
            'min_qty' => 1,
            'is_in_stock' => false,
            'is_allow_empty_qty' => false
        ];
        return [
            [$data1, true],
            [$data2, true],
            [$data3, true],
            [$data4, false]
        ];
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider provideStockItemQty
     */
    public function testCheckStockItemQty($data, $expected)
    {
        $helperMock = $this->getMockObjectWithoutConstructor(
            Data::class,
            [
                'getConfig',
                'verifyStockItem'
            ]
        );
        $configMock = $this->getMockObjectWithoutConstructor(
            Config::class,
            ['preordersEnabled']
        );
        $stockItemMock = $this->getMockObjectWithoutConstructor(
            StockItem::class,
            [
                'getBackorders'
            ]
        );
        $helperMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($configMock);
        $helperMock->expects($this->any())
            ->method('verifyStockItem')
            ->willReturn($data['is_stock_allowed']);
        $configMock->expects($this->any())
            ->method('preordersEnabled')
            ->willReturn($data['is_preorders_enabled']);
        $stockItemMock->expects($this->any())
            ->method('getBackorders')
            ->willReturn($data['backorder_option']);

        $result = $helperMock->checkStockItemQty($stockItemMock);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function provideStockItemQty()
    {
        $data1 = [
            'is_stock_allowed' => true,
            'is_preorders_enabled' => true,
            'backorder_option' => Data::BACKORDERS_PREORDER_OPTION
        ];
        $data2 = [
            'is_stock_allowed' => true,
            'is_preorders_enabled' => false,
            'backorder_option' => Data::BACKORDERS_PREORDER_OPTION
        ];
        $data3 = [
            'is_stock_allowed' => false,
            'is_preorders_enabled' => true,
            'backorder_option' => Data::BACKORDERS_PREORDER_OPTION
        ];
        $data4 = [
            'is_stock_allowed' => true,
            'is_preorders_enabled' => true,
            'backorder_option' => 222
        ];
        return [
            [$data1, true],
            [$data2, false],
            [$data3, false],
            [$data4, false]
        ];
    }
}
