<?php
namespace Ewave\ProductPriority\Test\Unit\Model\Priority\Sort;

/**
 * Class RevenueSortTest
 * @package Ewave\ProductPriority\Test\Unit\Model\Priority\Sort
 */
class RevenueSortTest extends SortTestAbstract
{
    const CLASS_NAME = 'Ewave\ProductPriority\Model\Priority\Sort\RevenueSort';
    const SORT = 'revenue';

    /**
     * Test _prepareItemProducts()
     */
    public function testPrepareItemProducts()
    {
        $actualQuery = [
            '2'  => [
                'item_id'        => '2',
                'parent_item_id' => null,
                'price'          => '0.0000',
            ],
            '17' => [
                'item_id'        => '17',
                'parent_item_id' => null,
                'price'          => '99.0000',
            ],
            '18' => [
                'item_id'        => '18',
                'parent_item_id' => '17',
                'price'          => '0.0000',
            ]
        ];
        $expectedQuery = [
            '2'  => [
                'item_id'        => '2',
                'parent_item_id' => null,
                'price'          => '0.0000',
            ],
            '17' => [
                'item_id'        => '17',
                'parent_item_id' => null,
                'price'          => '99.0000',
            ],
            '18' => [
                'item_id'        => '18',
                'parent_item_id' => '17',
                'price'          => '99.0000',
            ]
        ];
        $method = new \ReflectionMethod(
            self::CLASS_NAME,
            '_prepareItemProducts'
        );
        $method->setAccessible(true);
        $this->assertEquals($expectedQuery, $method->invoke($this->_prioritySort, $actualQuery));
    }

    /**
     * Test _sortByRevenue()
     */
    public function testSortByRevenue()
    {
        $actualQuery = [
            [
                'product_id'  => '1479',
                'qty_ordered' => '4.0000',
                'price'       => '33.0000',
            ],
            [
                'product_id'  => '1495',
                'qty_ordered' => '2.0000',
                'price'       => '28.0000',
            ],
            [
                'product_id'  => '335',
                'qty_ordered' => '1.0000',
                'price'       => '99.0000',
            ],
        ];
        $expectedQuery = [
            '1479' => 132.0000,
            '335'  => 99.0000,
            '1495' => 56.0000,
        ];

        $this->_configHelper->expects($this->any())
            ->method('getSortBy')
            ->willReturn(self::SORT);
        $method = new \ReflectionMethod(
            self::CLASS_NAME,
            '_sortByRevenue'
        );
        $method->setAccessible(true);
        $this->assertEquals($expectedQuery, $method->invoke($this->_prioritySort, $actualQuery));
    }

    /**
     * Test _sortCategoryProducts()
     * @param array $products
     * @param array $newProducts
     * @param array $inStockProducts
     * @param array $itemsArray
     * @param array $expectedResult
     * @dataProvider sortCategoryProductsDataProvider
     */
    public function testSortCategoryProducts($products, $newProducts, $inStockProducts, $itemsArray, $expectedResult)
    {
        $method = new \ReflectionMethod(
            self::CLASS_NAME,
            '_sortCategoryProducts'
        );
        $method->setAccessible(true);
        $this->assertEquals(
            $expectedResult,
            $method->invoke($this->_prioritySort, $products, $newProducts, $inStockProducts, $itemsArray)
        );
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function sortCategoryProductsDataProvider()
    {
        return [
            [
                [15, 2, 6, 4, 5, 7, 8, 14, 10, 11, 12, 13, 1, 3, 9], // Category products
                [1 => 0, 3 => 1, 7 => 2, 11 => 3], // New products
                [1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5, 7 => 6], // In stock products
                [7 => 100, 5 => 99, 3 => 55, 1 => 55, 15 => 55, 11 => 4], // Priority sort
                // Expected result
                [7, 1, 3, 5, 2, 4, 6, 11, 15, 8, 9, 10, 12, 13, 14],
            ],
            [
                [15, 2, 6, 4, 5, 7, 8, 14, 10, 11, 12, 13, 1, 3, 9], // Category products
                [], // New products
                [1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5, 7 => 6], // In stock products
                [7 => 100, 5 => 99, 3 => 55, 1 => 55, 15 => 55, 11 => 4], // Priority sort
                // Expected result
                [7, 5, 1, 3, 2, 4, 6, 15, 11, 8, 9, 10, 12, 13, 14],
            ],
            [
                [15, 2, 6, 4, 5, 7, 8, 14, 10, 11, 12, 13, 1, 3, 9], // Category products
                [1 => 0, 3 => 1, 7 => 2, 11 => 3], // New products
                [], // In stock products
                [7 => 100, 5 => 99, 3 => 55, 1 => 55, 15 => 55, 11 => 4], // Priority sort
                // Expected result
                [7, 1, 3, 11, 5, 15, 2, 4, 6, 8, 9, 10, 12, 13, 14],
            ],
            [
                [15, 2, 6, 4, 5, 7, 8, 14, 10, 11, 12, 13, 1, 3, 9], // Category products
                [1 => 0, 3 => 1, 7 => 2, 11 => 3], // New products
                [1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5, 7 => 6], // In stock products
                [], // Priority sort
                // Expected result
                [1, 3, 7, 2, 4, 5, 6, 11, 8, 9, 10, 12, 13, 14, 15],
            ],
            [
                [15, 2, 6, 4, 5, 7, 8, 14, 10, 11, 12, 13, 1, 3, 9], // Category products
                [], // New products
                [], // In stock products
                [7 => 100, 5 => 99, 3 => 55, 1 => 55, 15 => 55, 11 => 4], // Priority sort
                // Expected result
                [7, 5, 1, 3, 15, 11, 2, 4, 6, 8, 9, 10, 12, 13, 14],
            ],
            [
                [15, 2, 6, 4, 5, 7, 8, 14, 10, 11, 12, 13, 1, 3, 9], // Category products
                [], // New products
                [1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5, 7 => 6], // In stock products
                [], // Priority sort
                // Expected result
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
            ],
            [
                [15, 2, 6, 4, 5, 7, 8, 14, 10, 11, 12, 13, 1, 3, 9], // Category products
                [1 => 0, 3 => 1, 7 => 2, 11 => 3], // New products
                [], // In stock products
                [], // Priority sort
                // Expected result
                [1, 3, 7, 11, 2, 4, 5, 6, 8, 9, 10, 12, 13, 14, 15],
            ],
            [
                [15, 2, 6, 4, 5, 7, 8, 14, 10, 11, 12, 13, 1, 3, 9], // Category products
                [], // New products
                [], // In stock products
                [], // Priority sort
                // Expected result
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
            ],
            [
                [], // Category products
                [1 => 0, 3 => 1, 7 => 2, 11 => 3], // New products
                [1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5, 7 => 6], // In stock products
                [7 => 100, 5 => 99, 3 => 55, 1 => 55, 15 => 55, 11 => 4], // Priority sort
                // Expected result
                [],
            ],
            [
                [], // Category products
                [], // New products
                [], // In stock products
                [], // Priority sort
                // Expected result
                [],
            ],
        ];
    }
}
