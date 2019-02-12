<?php

namespace Ewave\Navigation\Test\Unit\Ui\DataProvider\Menu\Form\Modifier;

use Ewave\Navigation\Model\Registry\Constants;
use Ewave\Navigation\Test\Unit\NavigationTestUnitTrait;

/**
 * Class MenuItemsTest
 * @package Ewave\Navigation\Test\Unit\Ui\DataProvider\Menu\Form\Modifier
 */
class MenuItemsTest extends \PHPUnit_Framework_TestCase
{
    use NavigationTestUnitTrait;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuItemsCollectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_setItemsCollectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuCollectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_setCollectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuItemsModifier;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuItemMock;

    /**
     * Set up objects
     * @return void
     */
    public function setUp()
    {
        $this->_objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->_menuItemsCollectionFactoryMock = $this->getMockObjectWithoutConstructor(
            'Ewave\Navigation\Model\ResourceModel\Menu\Grid\CollectionFactory',
            [
                'create'
            ]
        );

        $this->_setItemsCollectionFactoryMock = $this->getMockObjectWithoutConstructor(
            'Ewave\Navigation\Model\ResourceModel\Set\Grid\CollectionFactory',
            [
                'create'
            ]
        );

        $this->_registryMock = $this->getMockObjectWithoutConstructor('Magento\Framework\Registry', ['registry']);

        $this->_menuCollectionMock = $this->getMockObjectWithoutConstructor(
            'Ewave\Navigation\Model\ResourceModel\Menu\Collection',
            [
                'addFieldToFilter',
                'setCurrentStoreId',
                '_beforeLoad',
                '_afterLoad',
                'getStoreId',
                'getData',
                'getItems'
            ]
        );

        $this->_setCollectionMock = $this->getMockObjectWithoutConstructor(
            'Ewave\Navigation\Model\ResourceModel\Set\Collection',
            [
                'addFieldToFilter',
                'setCurrentStoreId',
                '_beforeLoad',
                '_afterLoad',
                'getStoreId',
                'getData',
                'getItems'
            ]
        );

        $this->_menuItemsModifier = $this->_objectManager->getObject(
            'Ewave\Navigation\Ui\DataProvider\Menu\Form\Modifier\MenuItems',
            [
                'registry' => $this->_registryMock,
                'menuCollectionFactory' => $this->_menuItemsCollectionFactoryMock,
                'setCollectionFactory' => $this->_setItemsCollectionFactoryMock
            ]
        );

        $this->_menuItemMock = $this->getMockObjectWithoutConstructor(
            'Ewave\Navigation\Model\Menu',
            [
                'getId'
            ]
        );



        $this->_menuItemMock->expects($this->any())
            ->method('getId')
            ->willReturn(4);

        $this->_registryMock->expects($this->at(1))
            ->method('registry')
            ->with(Constants::CURRENT_STORE_ID)
            ->willReturn(0);

        $this->_registryMock->expects($this->at(0))
            ->method('registry')
            ->with(Constants::CURRENT_MENU_ITEM)
            ->willReturn($this->_menuItemMock);
    }

    /**
     * Test modify meta - test menu items property
     *
     * @dataProvider modifyMetadataProvider
     * @param $data
     * @param $expected
     */
    public function testModifyMeta($data, $expected)
    {
        $this->_menuItemsCollectionFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->_menuCollectionMock);

        $this->_setItemsCollectionFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->_setCollectionMock);

        $this->_menuCollectionMock->expects($this->any())
            ->method('addFieldToFilter')
            ->willReturnSelf();

        $this->_menuCollectionMock->expects($this->any())
            ->method('getItems')
            ->willReturn($data['items']);
        $this->_setCollectionMock->expects($this->any())
            ->method('getItems')
            ->willReturn($data['sets']);
        $result = $this->_menuItemsModifier->modifyMeta([]);

        $optionsNode = $result['menu_item_information']['children']['parent_menu_item_id']
        ['arguments']['data']['config']['options'];

        $this->assertEquals($expected, $optionsNode);
    }

    /**
     * Return processed menu items
     *
     * @return array
     */
    public function modifyMetaDataProvider()
    {
        $set1 = $this->_getSetItem(1, 'First Set');
        $set2 = $this->_getSetItem(2, 'Second Set');
        $set3 = $this->_getSetItem(3, 'Third Set');

        $item1 = $this->_getMenuItem(1, 0, '0/1', 1, 'First Level Menu Item First', 1);
        $item2 = $this->_getMenuItem(2, 0, '0/2', 1, 'First Level Menu Item Second', 2);
        $item3 = $this->_getMenuItem(3, 1, '0/1/2', 1, 'Second Level Child First Level Menu Item First', 3);
        $item4 = $this->_getMenuItem(
            4,
            3,
            '0/1/3/4',
            1,
            'Third Level Menu Item Second Level Child First Level Menu Item First',
            1
        );
        $item5 = $this->_getMenuItem(5, 2, '0/2/5', 1, 'Second level menu item child First Level Menu Item Second', 2);
        $items1 = [$item1, $item2, $item3, $item4, $item5];

        $item4 = $this->_getMenuItem(4, 0, '0/4', 1, 'Menu Item 4 level 1', 3);
        $items2 = [$item1, $item2, $item4];
        return [
            [
                [
                    'sets' => [$set1, $set2, $set3],
                    'items' => $items1
                ],
                [
                    [
                        'value' => 'set1',
                        'is_active' => 'true',
                        'label' => 'First Set',
                        'is_nonclickable' => true,
                        'optgroup' => [
                            [
                                'value' => 1,
                                'is_active' => 1,
                                'label' => 'First Level Menu Item First',
                                'optgroup' => [
                                    [
                                        'value' => 3,
                                        'is_active' => 1,
                                        'label' => 'Second Level Child First Level Menu Item First',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'value' => 'set2',
                        'is_active' => 'true',
                        'label' => 'Second Set',
                        'is_nonclickable' => true,
                        'optgroup' => [
                            [
                                'value' => 2,
                                'is_active' => 1,
                                'label' => 'First Level Menu Item Second',
                                'optgroup' => [
                                    [
                                        'value' => 5,
                                        'is_active' => 1,
                                        'label' => 'Second level menu item child First Level Menu Item Second',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'value' => 'set3',
                        'is_active' => 'true',
                        'label' => 'Third Set',
                        'is_nonclickable' => true
                    ],
                    'empty' => [
                        'value' => '0',
                        'label' => __('No parent item')
                    ]
                ]
            ],

            [
                [
                    'sets' => [$set1, $set2, $set3],
                    'items' => $items2
                ],
                [
                    [
                        'value' => 'set1',
                        'is_active' => 'true',
                        'label' => 'First Set',
                        'is_nonclickable' => true,
                        'optgroup' => [
                            [
                                'value' => 1,
                                'is_active' => 1,
                                'label' => 'First Level Menu Item First',
                            ]
                        ]
                    ],
                    [
                        'value' => 'set2',
                        'is_active' => 'true',
                        'label' => 'Second Set',
                        'is_nonclickable' => true,
                        'optgroup' => [
                            [
                                'value' => 2,
                                'is_active' => 1,
                                'label' => 'First Level Menu Item Second'
                            ]
                        ]
                    ],
                    [
                        'value' => 'set3',
                        'is_active' => 'true',
                        'label' => 'Third Set',
                        'is_nonclickable' => true
                    ],
                    'empty' => [
                        'value' => '0',
                        'label' => __('No parent item')
                    ]
                ]
            ],

        ];
    }

    /**
     * Get new processed menu item mock
     *
     * @param $id
     * @param $parentMenuItemId
     * @param $path
     * @param $status
     * @param $title
     * @param $menuSetId
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMenuItem($id, $parentMenuItemId, $path, $status, $title, $menuSetId)
    {
        $menuItem = $this->getMockObjectWithoutConstructor(
            'Ewave\Navigation\Model\Menu',
            [
                'getTitle',
                'getId',
                'getStatus',
                'getPath',
                'getParentMenuItemId',
                'getMenuSetId'
            ]
        );

        $menuItem->expects($this->any())
            ->method('getStatus')
            ->willReturn($status);

        $menuItem->expects($this->any())
            ->method('getPath')
            ->willReturn($path);

        $menuItem->expects($this->any())
            ->method('getParentMenuItemId')
            ->willReturn($parentMenuItemId);

        $menuItem->expects($this->any())
            ->method('getTitle')
            ->willReturn($title);

        $menuItem->expects($this->any())
            ->method('getId')
            ->willReturn($id);

        $menuItem->expects($this->any())
            ->method('getMenuSetId')
            ->willReturn($menuSetId);
        return $menuItem;
    }

    /**
     * Get new processed set item mock
     *
     * @param int $id
     * @param string $name
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getSetItem($id, $name)
    {
        /**
         * @var \PHPUnit_Framework_MockObject_MockObject $menuItem
         */
        $menuItem = $this->getMockObjectWithoutConstructor(
            'Ewave\Navigation\Model\Set',
            [
                'getId',
                'getName'
            ]
        );

        $menuItem->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $menuItem->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        return $menuItem;
    }
}
