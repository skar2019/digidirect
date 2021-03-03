<?php
namespace Digidirect\Navigation\Test\Unit\Helper;

use Digidirect\Navigation\Test\Unit\NavigationTestUnitTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class DataTest
 * @package Digidirect\Navigation\Test\Unit\Helper
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    use NavigationTestUnitTrait;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * @var \Digidirect\Navigation\Helper\Data
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * Setup objects
     *
     * @return void
     */
    public function setUp()
    {
        $this->_objectManager = new ObjectManager($this);

        $this->_contextMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\App\Helper\Context',
            ['getRequest']
        );

        $this->_storeManagerMock = $this->getMockObjectWithoutConstructor(
            'Magento\Store\Model\StoreManager',
            ['getStore']
        );
        $this->_appStateMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\App\State',
            ['getAreaCode']
        );

        $this->_storeMock = $this->getMockObjectWithoutConstructor(
            'Magento\Store\Model\Store',
            ['getId']
        );

        $this->_storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($this->_storeMock);

        $this->_requestMock = $this->_getRequestMockWithoutConstructor(['getParam']);

        $this->_contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->_requestMock);

        $this->_helper = $this->_objectManager->getObject(
            'Digidirect\Navigation\Helper\Data',
            [
                'appState' => $this->_appStateMock,
                'context' => $this->_contextMock,
                'storeManager' => $this->_storeManagerMock
            ]
        );
    }

    /**
     * Test get current store
     *
     * @dataProvider provideDataGetCurrentStoreId
     * @param [] $data
     * @param string $expected
     */
    public function testGetCurrentStoreId($data, $expected)
    {
        $this->_appStateMock->expects($this->any())
            ->method('getAreaCode')
            ->willReturn($data['area_code']);

        $this->_requestMock->expects($this->any())
            ->method('getParam')
            ->with('store')
            ->willReturn($data['request_store_id']);

        $this->_storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($data['real_current_store_id']);

        $this->assertEquals($expected, $this->_helper->getCurrentStoreId());
    }

    /**
     * Provide data
     *
     * @return array
     */
    public function provideDataGetCurrentStoreId()
    {
        return [
            [['area_code' => 'frontend', 'request_store_id' => null, 'real_current_store_id' => 1], 1],
            [['area_code' => 'frontend', 'request_store_id' => 1, 'real_current_store_id' => 2], 2],
            [['area_code' => 'frontend', 'request_store_id' => 0, 'real_current_store_id' => 1], 1],
            [['area_code' => 'adminhtml', 'request_store_id' => 0, 'real_current_store_id' => 1], 0],
            [['area_code' => 'adminhtml', 'request_store_id' => null, 'real_current_store_id' => 1],0],
            [['area_code' => 'adminhtml', 'request_store_id' => 1, 'real_current_store_id' => 1], 1],
            [['area_code' => 'adminhtml', 'request_store_id' => 2, 'real_current_store_id' => 1], 2]
        ];
    }
}
