<?php
namespace Ewave\ProductPriority\Test\Unit\Model\Priority\Sort;

/**
 * Class PriorityFactory
 * @package Ewave\ProductPriority\Test\Unit\Model\Priority\Sort
 */
class PriorityFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var
     */
    protected $_priorityFactory;

    /**
     * @var
     */
    protected $_configHelper;

    /**
     * @var
     */
    protected $_objectManagerMock;

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->_configHelper = $this->getMockBuilder('Ewave\ProductPriority\Helper\Config')
            ->setMethods(['getSortBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->_objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_priorityFactory = $this->_objectManager->getObject(
            'Ewave\ProductPriority\Model\PriorityFactory',
            [
                'config' => $this->_configHelper,
                'objectManager' => $this->_objectManagerMock,
                'sortTypes' => [
                    'revenue' => $this->_objectManager
                        ->getObject('Ewave\ProductPriority\Model\Priority\Sort\RevenueSort'),
                    'number_of_sale' => $this->_objectManager
                        ->getObject('Ewave\ProductPriority\Model\Priority\Sort\NumberOfSalesSort'),
                    'margin' => $this->_objectManager
                        ->getObject('Ewave\ProductPriority\Model\Priority\Sort\MarginSort'),
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function testCreateRevenueSort()
    {
        $this->_configHelper->expects($this->any())
            ->method('getSortBy')
            ->willReturn('revenue');
        $this->assertInstanceOf(
            'Ewave\ProductPriority\Model\Priority\Sort\RevenueSort',
            $this->_priorityFactory->create()
        );
    }

    /**
     * @inheritdoc
     */
    public function testCreateMarginSort()
    {
        $this->_configHelper->expects($this->any())
            ->method('getSortBy')
            ->willReturn('margin');
        $this->assertInstanceOf(
            'Ewave\ProductPriority\Model\Priority\Sort\MarginSort',
            $this->_priorityFactory->create()
        );
    }

    /**
     * @inheritdoc
     */
    public function testCreateNumberOfSalesSortSort()
    {
        $this->_configHelper->expects($this->any())
            ->method('getSortBy')
            ->willReturn('number_of_sale');
        $this->assertInstanceOf(
            'Ewave\ProductPriority\Model\Priority\Sort\NumberOfSalesSort',
            $this->_priorityFactory->create()
        );
    }

    /**
     * @inheritdoc
     */
    public function testCreate()
    {
        $className = 'Ewave\ProductPriority\Model\Priority\Sort\NumberOfSalesSort';
        $filterMock = $this->getMock($className, [], [], '', false);
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $className,
            []
        )->will(
            $this->returnValue($filterMock)
        );
        $this->assertEquals($filterMock, $this->_priorityFactory->create($className));
    }

    /**
     * @inheritdoc
     */
    public function testCreateWithArguments()
    {
        $className = 'Ewave\ProductPriority\Model\Priority\Sort\MarginSort';
        $arguments = ['some', 'arguments'];
        $filterMock = $this->getMock($className, [], [], '', false);
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $className,
            $arguments
        )->will(
            $this->returnValue($filterMock)
        );
        $this->assertEquals($filterMock, $this->_priorityFactory->create($className, $arguments));
    }

    /**
     * @inheritdoc
     */
    public function testCreateWrongSortTypeException()
    {
        $wrongSortType = 'wrong_sort_type';
        $this->_configHelper->expects($this->any())
            ->method('getSortBy')
            ->willReturn($wrongSortType);
        $this->setExpectedException(
            'Magento\Framework\Exception\LocalizedException',
            $wrongSortType . ' Undefined sort type'
        );
        $this->assertInstanceOf(
            'Ewave\ProductPriority\Model\Priority\Sort\NumberOfSalesSort',
            $this->_priorityFactory->create()
        );
    }

    /**
     * @inheritdoc
     */
    public function testCreateWrongTypeException()
    {
        $className = 'WrongClass';
        $filterMock = $this->getMock($className, [], [], '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->will($this->returnValue($filterMock));
        $this->setExpectedException(
            'Magento\Framework\Exception\LocalizedException',
            $className . ' doesn\'t extends \Ewave\ProductPriority\Model\Priority\CalculateAbstract'
        );
        $this->_priorityFactory->create($className);
    }
}
