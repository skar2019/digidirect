<?php
namespace Ewave\ProductPriority\Test\Unit\Model\Priority\Sort;

/**
 * Class MarginSortTest
 * @package Ewave\ProductPriority\Test\Unit\Model\Priority\Sort
 */
class MarginSortTest extends SortTestAbstract
{
    const CLASS_NAME = 'Ewave\ProductPriority\Model\Priority\Sort\MarginSort';
    const SORT = 'margin';

    /**
     * @inheritdoc
     */
    public function testSortByMargin()
    {
        $actualQuery = [
            [
                'product_id'         => '1479',
                'qty_ordered'        => '1.0000',
                'product_item_price' => '0.0000',
                'product_price'      => '33.0000',
                'cost'               => null,
                'stock_status'       => '1'
            ],
            [
                'product_id'         => '1495',
                'qty_ordered'        => '1.0000',
                'product_item_price' => '28.0000',
                'product_price'      => '28.0000',
                'cost'               => null,
                'stock_status'       => '1'
            ],
            [
                'product_id'         => '335',
                'qty_ordered'        => '2.0000',
                'product_item_price' => '99.0000',
                'product_price'      => '99.0000',
                'cost'               => '7.0000',
                'stock_status'       => '1'
            ],
        ];
        $expectedQuery = [
            '335'  => 184,
            '1495' => 0,
            '1479' => -33,
        ];
        $this->_configHelper->expects($this->any())
            ->method('getSortBy')
            ->willReturn(self::SORT);
        $method = new \ReflectionMethod(
            self::CLASS_NAME,
            '_sortByMargin'
        );
        $method->setAccessible(true);
        $this->assertEquals($expectedQuery, $method->invoke($this->_prioritySort, $actualQuery));
    }

    /**
     * @inheritdoc
     */
    public function testCalculateMarginForOrderItem()
    {
        $actualQuery = [
            'product_id'         => '335',
            'qty_ordered'        => '2.0000',
            'product_item_price' => '99.0000',
            'product_price'      => '99.0000',
            'cost'               => '7.0000'
        ];
        $method = new \ReflectionMethod(
            self::CLASS_NAME,
            '_calculateMarginForOrderItem'
        );
        $method->setAccessible(true);
        $this->assertEquals(184, $method->invoke($this->_prioritySort, $actualQuery));
    }

    /**
     * @inheritdoc
     */
    public function testPrepareItemProducts()
    {
        $actualQuery = [
            '2'  => [
                'item_id'            => '2',
                'parent_item_id'     => null,
                'product_item_price' => '0.0000',
                'cost'               => null
            ],
            '17' => [
                'item_id'            => '17',
                'parent_item_id'     => null,
                'product_item_price' => '99.0000',
                'cost'               => null
            ],
            '18' => [
                'item_id'            => '18',
                'parent_item_id'     => '17',
                'product_item_price' => '0.0000',
                'cost'               => '7.0000'
            ]
        ];
        $expectedQuery = [
            '2'  => [
                'item_id'            => '2',
                'parent_item_id'     => null,
                'product_item_price' => '0.0000',
                'cost'               => null
            ],
            '17' => [
                'item_id'            => '17',
                'parent_item_id'     => null,
                'product_item_price' => '99.0000',
                'cost'               => '7.0000'
            ],
            '18' => [
                'item_id'            => '18',
                'parent_item_id'     => '17',
                'product_item_price' => '99.0000',
                'cost'               => '7.0000'
            ]
        ];
        $method = new \ReflectionMethod(
            self::CLASS_NAME,
            '_prepareItemProducts'
        );
        $method->setAccessible(true);
        $this->assertEquals($expectedQuery, $method->invoke($this->_prioritySort, $actualQuery));
    }
}
