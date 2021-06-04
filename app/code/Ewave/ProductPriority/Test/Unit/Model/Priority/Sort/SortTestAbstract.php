<?php
namespace Ewave\ProductPriority\Test\Unit\Model\Priority\Sort;

/**
 * Class MarginSortTest
 * @package Ewave\ProductPriority\Test\Unit\Model\Priority\Sort
 */
abstract class SortTestAbstract extends \PHPUnit_Framework_TestCase
{
    const CLASS_NAME = '';

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var
     */
    protected $_prioritySort;

    /**
     * @var
     */
    protected $_configHelper;

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->_configHelper = $this->getMockBuilder('Ewave\ProductPriority\Helper\Config')
            ->setMethods(['getSortBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $stockItemRepository = $this->getMockBuilder('Magento\CatalogInventory\Model\Stock\StockItemRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $resource = $this->getMockBuilder('Magento\Framework\App\ResourceConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $indexerFactory = $this->getMockBuilder('Magento\Indexer\Model\IndexerFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $productCollectionFactory = $this->getMockBuilder(
            'Magento\Catalog\Model\ResourceModel\Product\CollectionFactory'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $catalogConfig = $this->getMockBuilder('Magento\Catalog\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_prioritySort = $this->_objectManager->getObject(
            static::CLASS_NAME,
            [
                'config'                   => $this->_configHelper,
                'stockItemRepository'      => $stockItemRepository,
                'resource'                 => $resource,
                'indexerFactory'           => $indexerFactory,
                'productCollectionFactory' => $productCollectionFactory,
                'catalogConfig'            => $catalogConfig,
                'reIndexTypes'             => []
            ]
        );
    }
}
