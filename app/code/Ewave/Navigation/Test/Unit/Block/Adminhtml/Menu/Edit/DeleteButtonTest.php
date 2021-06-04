<?php
namespace Ewave\Navigation\Test\Unit\Block\Adminhtml\Menu\Edit;

use Ewave\Navigation\Model\Registry\Constants;
use Ewave\Navigation\Test\Unit\NavigationTestUnitTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class DeleteButtonTest
 * @package Ewave\Navigation\Test\Unit\Block\Adminhtml\Menu\Edit
 */
class DeleteButtonTest extends \PHPUnit_Framework_TestCase
{
    use NavigationTestUnitTrait;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Ewave\Navigation\Block\Adminhtml\Menu\Edit\DeleteButton
     */
    protected $_deleteButton;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registryMock;

    /**
     * Setup objects
     * @return void
     */
    public function setUp()
    {
        $this->_objectManager = new ObjectManager($this);

        $this->_registryMock = $this->_getRegistryMockWithoutConstructor(['registry']);

        $this->_deleteButton = $this->_objectManager->getObject(
            'Ewave\Navigation\Block\Adminhtml\Menu\Edit\DeleteButton',
            ['registry' => $this->_registryMock]
        );
    }

    /**
     * Test message before deleting
     *
     * @dataProvider provideDataDeleteButton
     * @param [] $data
     * @param string $expected
     */
    public function testGetButtonData($data, $expected)
    {
        $deleteButton = $this->_objectManager->getObject(
            'Ewave\Navigation\Block\Adminhtml\Menu\Edit\DeleteButton',
            ['registry' => $this->_registryMock]
        );

        $menuItemMock = $data['menu_item'];
        $resourceModelMock = $this->getMockObjectWithoutConstructor(
            'Ewave\Navigation\Model\ResourceModel\Menu',
            ['getChildrenIds']
        );
        $this->_registryMock->expects($this->any())
            ->method('registry')
            ->with(Constants::CURRENT_MENU_ITEM)
            ->willReturn($menuItemMock);

        $menuItemMock->expects($this->any())
            ->method('getId')
            ->willReturn($data['menu_id']);

        $menuItemReflection = new \ReflectionClass(\Ewave\Navigation\Model\Menu::class);
        $property = $menuItemReflection->getProperty('role');
        $property->setAccessible(true);

        $roleMock = $this->getMockObjectWithoutConstructor(
            \Magento\AdminGws\Model\Role::class,
            ['getIsAll']
        );

        $roleMock->expects($this->any())
            ->method('getIsAll')
            ->willReturn(true);

        $property->setValue($menuItemMock, $roleMock);

        $menuItemMock->expects($this->any())
            ->method('_getResource')
            ->willReturn($resourceModelMock);

        $resourceModelMock->expects($this->any())
            ->method('getChildrenIds')
            ->willReturn($data['children']);

        $result = $deleteButton->getButtonData();

        $this->assertEquals($expected, $result['on_click']);
    }

    /**
     * Provide processed menu items
     *
     * @return array
     */
    public function provideDataDeleteButton()
    {
        $menuItem1 = $this->_getMenuItemModelMockWithoutConstructor(['_getResource', 'getId']);
        $data = [
            'menu_item' => $menuItem1,
            'children' => [1, 2],
            'menu_id' => 145
        ];

        $menuItem2 = $this->_getMenuItemModelMockWithoutConstructor(['_getResource', 'getId']);

        $data2 = [
            'menu_item' => $menuItem2,
            'children' => [],
            'menu_id' => 64
        ];
        return [
            [
                $data,
                'deleteConfirm(\'There are menu items assigned to this parent item.' .
                ' Are you sure you want to delete this menu item and all the menu items assigned to it?\', \'\')'
            ],
            [
                $data2,
                'deleteConfirm(\'Are you sure you want to delete this?\', \'\')'
            ]
        ];
    }
}
