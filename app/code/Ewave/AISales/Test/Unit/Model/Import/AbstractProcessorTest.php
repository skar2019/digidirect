<?php
namespace Ewave\AISales\Test\Unit\Model\Import;

use Ewave\AISales\Model\Import\Order\Model\Processor as AbstractProcessor;
use Ewave\Utilities\Test\Unit\Library;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class AbstractProcessorTest extends Library
{
    /**
     * @var AbstractProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $processor;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $objectManager = new ObjectManager($this);
        $this->processor = $objectManager->getObject(AbstractProcessor::class);

        $this->connectionMock = $this->getMockObjectWithoutConstructor(
            \Magento\Framework\DB\Adapter\Pdo\Mysql::class,
            [
                'fetchPairs',
                'insertOnDuplicate',
                'insertMultiple',
                'select',
                'from',
                'where',
                'query',
                'describeTable',
                'getTableName',
                'fetchAll',
            ]
        );

        $connectionProperty = new \ReflectionProperty(AbstractProcessor::class, 'connection');
        $connectionProperty->setAccessible(true);
        $connectionProperty->setValue($this->processor, $this->connectionMock);
    }

    /**
     * Test save orders
     */
    public function testSaveOrders()
    {
        $this->connectionMock->expects($this->any())
            ->method('select')
            ->willReturnSelf();

        $this->connectionMock->expects($this->any())
            ->method('from')
            ->willReturnSelf();

        $this->connectionMock->expects($this->any())
            ->method('describeTable')
            ->willReturn(['increment_id' => [], 'store_id' => []]);

        $this->connectionMock->expects($this->any())
            ->method('fetchAll')
            ->willReturn([]);

        $this->connectionMock->expects($this->any())
            ->method('fetchPairs')
            ->willReturn($this->getExistingOrders());

        $this->connectionMock->expects($this->at(2))
            ->method('insertMultiple');

        $this->connectionMock->expects($this->once())
            ->method('insertOnDuplicate');

        $this->processor->save($this->provideOrdersDataForSave());
    }

    /**
     * Test update orders
     */
    public function testUpdateOrders()
    {
        $this->connectionMock->expects($this->any())
            ->method('select')
            ->willReturnSelf();

        $this->connectionMock->expects($this->any())
            ->method('from')
            ->willReturnSelf();

        $this->connectionMock->expects($this->any())
            ->method('describeTable')
            ->willReturn(['increment_id' => [], 'store_id' => []]);

        $this->connectionMock->expects($this->any())
            ->method('fetchAll')
            ->willReturn([]);

        $this->connectionMock->expects($this->any())
            ->method('fetchPairs')
            ->willReturn($this->getExistingOrders());

        $this->connectionMock->expects($this->never())
            ->method('insertMultiple');

        $this->connectionMock->expects($this->at(2))
            ->method('insertOnDuplicate');

        $this->processor->update($this->provideOrdersDataForSave());
    }

    /**
     * Provide integration data
     *
     * @return []
     */
    public function provideOrdersDataForSave()
    {
        return [
            [
                'increment_id' => 'order_001',
                'store_id' => 1,
                'grand_total' => 999
            ],
            [
                'increment_id' => 'order_002',
                'store_id' => 1,
                'grand_total' => 514
            ],
            [
                'increment_id' => 'order_003',
                'store_id' => 1,
                'grand_total' => 98
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getExistingOrders()
    {
        return [
            'order_001_1' => 1,
            'order_002_1' => 2,
        ];
    }
}
