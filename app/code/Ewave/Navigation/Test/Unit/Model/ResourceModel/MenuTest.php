<?php
namespace Ewave\Navigation\Test\Unit\Model\ResourceModel;

use Ewave\Navigation\Test\Unit\NavigationTestUnitTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class MenuTest
 * @package Ewave\Navigation\Test\Unit\Model\ResourceModel
 */
class MenuTest extends \PHPUnit_Framework_TestCase
{
    use NavigationTestUnitTrait;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Ewave\Navigation\Model\ResourceModel\Menu
     */
    protected $_menuResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_connection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourcesMock;

    /**
     * Setup objects
     * @return void
     */
    public function setUp()
    {
        $this->_objectManager = new ObjectManager($this);

        $dbContext = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\Model\ResourceModel\Db\Context',
            ['getResources']
        );

        $this->_resourcesMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\App\ResourceConnection',
            ['getConnection', 'getTableName']
        );

        $connection = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\DB\Adapter\Pdo\Mysql',
            ['select', 'from', 'where', 'fetchOne', 'quoteInto']
        );

        $connection->expects($this->any())
            ->method('select')
            ->willReturnSelf();
        $connection->expects($this->any())
            ->method('from')
            ->willReturnSelf();
        $connection->expects($this->any())
            ->method('innerJoin')
            ->willReturnSelf();
        $connection->expects($this->any())
            ->method('joinInner')
            ->willReturnSelf();
        $connection->expects($this->any())
            ->method('where')
            ->willReturnSelf();
        $connection->expects($this->any())
            ->method('group')
            ->willReturnSelf();
        $connection->expects($this->any())
            ->method('order')
            ->willReturnSelf();
        $connection->expects($this->any())
            ->method('reset')
            ->willReturnSelf();

        $connection->expects($this->any())
            ->method('quoteInto')
            ->willReturn('some_query');

        $this->_connection = $connection;

        $dbContext->expects($this->any())
            ->method('getResources')
            ->willReturn($this->_resourcesMock);

        $this->_resourcesMock->expects($this->any())
            ->method('getConnection')
            ->with('default')
            ->willReturn($this->_connection);

        $this->_menuResource = $this->_objectManager->getObject(
            'Ewave\Navigation\Model\ResourceModel\Menu',
            [
                'context' => $dbContext
            ]
        );
    }

    /**
     * Test validate
     *
     * @dataProvider provideValidationData
     * @param [] $data
     * @param [] $expected
     * @return void
     */
    public function testValidate($data, $expected)
    {
        $menuId = $data['menu_id'];
        $storeId = $data['store_id'];
        $parentMenuItemId = $data['parent_menu_item_id'];
        $selectResult = $data['select_result'];
        $requestMock = $this->_getRequestMockWithoutConstructor(['getParam']);

        $this->_resourcesMock->expects($this->any())
            ->method('getTableName')
            ->willReturn('ewave_navigation_menu_item_info');

        $this->_connection->expects($this->any())
            ->method('fetchOne')
            ->willReturn($selectResult);

        $requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('parent_menu_item_id')
            ->willReturn($parentMenuItemId);

        $this->assertEquals($expected, $this->_menuResource->validate($requestMock, $storeId, $menuId));
    }

    /**
     * Provide validation data
     *
     * @return []
     */
    public function provideValidationData()
    {
        return [
            [
                [
                    'menu_id' => 2,
                    'store_id' => 16,
                    'parent_menu_item_id' => 0,
                    'select_result' => 0
                ],
                []
            ],
            [
                [
                    'menu_id' => 2,
                    'store_id' => 16,
                    'parent_menu_item_id' => 3,
                    'select_result' => 2
                ],
                [
                    __('Specified parent menu item is already used as child of current item'),
                    __('Menu item code must be unique')
                ]
            ]
        ];
    }
}
