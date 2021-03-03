<?php
namespace Digidirect\Navigation\Test\Unit\Block;

use Digidirect\Navigation\Test\Unit\NavigationTestUnitTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class MenuTest
 * @package Digidirect\Navigation\Test\Unit\Block
 */
class MenuTest extends \PHPUnit_Framework_TestCase
{
    use NavigationTestUnitTrait;

    /**
     * @var \Digidirect\Navigation\Block\Menu
     */
    protected $_menuBlock;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuCollectionFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuCollection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mockUrl;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_context;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelperReal;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $menuRepositoryMock;

    /**
     * Setup objects
     * @return void
     */
    public function setUp()
    {
        $this->_objectManager = new ObjectManager($this);

        $this->_menuCollectionFactory = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Model\ResourceModel\Menu\CollectionFactory',
            ['create']
        );

        $this->menuRepositoryMock = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Model\ResourceModel\MenuRepository',
            ['getListBySetId']
        );

        $this->_menuCollection = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Model\ResourceModel\Menu\Collection',
            [
                'addOrder',
                'addSetFilter',
                'joinTypeInfo',
                'addFieldToFilter',
                'addFullInfoToSelect',
                'getItems',
                'getIterator'
            ]
        );
        $this->_context = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\View\Element\Template\Context',
            ['getUrlBuilder', 'getEventManager']
        );

        $this->_mockUrl = $this->getMockObjectWithoutConstructor('Magento\Framework\Url', ['getCurrentUrl']);

        $this->_context->expects($this->any())
            ->method('getUrlBuilder')
            ->willReturn($this->_mockUrl);

        $this->_menuCollectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->_menuCollection);

        $this->_menuCollection->expects($this->any())
            ->method('addFieldToFilter')
            ->willReturnSelf();

        $this->_menuCollection->expects($this->any())
            ->method('addSetFilter')
            ->willReturnSelf();

        $this->_menuCollection->expects($this->any())
            ->method('joinTypeInfo')
            ->willReturnSelf();

        $this->_menuCollection->expects($this->any())
            ->method('addFullInfoToSelect')
            ->willReturnSelf();

        $this->_menuCollection->expects($this->any())
            ->method('addOrder')
            ->willReturnSelf();

        $this->_jsonHelperReal = $this->_objectManager->getObject(
            'Magento\Framework\Json\Helper\Data',
            [
                'jsonEncoder' => $this->_objectManager->getObject('Magento\Framework\Json\Encoder'),
                'jsonDecoder' => $this->_objectManager->getObject('Magento\Framework\Json\Decoder')
            ]
        );

        $eventManager = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\Event\Manager',
            [
                'dispatch'
            ]
        );

        $this->_context->expects($this->any())
            ->method('getEventManager')
            ->willReturn($eventManager);

        $this->_menuBlock = $this->_objectManager->getObject(
            'Digidirect\Navigation\Block\Menu',
            [
                'collectionFactory' => $this->_menuCollectionFactory,
                'data' => [
                    'set_code' => 'test_set_code'
                ],
                'context' => $this->_context,
                'jsonHelper' => $this->_jsonHelperReal,
                'menuRepository' => $this->menuRepositoryMock,
            ]
        );
    }

    /**
     * Test get menu (recursion)
     *
     * @dataProvider provideMenuItemsData
     * @return void
     */
    public function testGetMenuJson($items, $expected)
    {
        $this->menuRepositoryMock->expects($this->any())
            ->method('getListBySetId')
            ->willReturn($this->_menuCollection);

        $iterator = new \ArrayIterator($items);
        $this->_menuCollection->expects($this->any())
            ->method('getIterator')
            ->willReturn($iterator);

        $this->_mockUrl->expects($this->any())
            ->method('getCurrentUrl')
            ->willReturn('http://homepage.com');

        $this->assertEquals($this->_jsonHelperReal->jsonEncode($expected), $this->_menuBlock->getMenuJson());
    }

    /**
     * Get processed menu items
     *
     * @return []
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function provideMenuItemsData()
    {
        $item1 = $this->_getMenuItemMock(
            1,
            0,
            'http://some.url',
            [
                'title' => 'Highest level',
                'identifier' => 'menu-node1',
                'custom_options' => []
            ],
            'category'
        );

        $item2 = $this->_getMenuItemMock(
            2,
            1,
            'http://some2.url',
            [
                'title' => 'Second level',
                'identifier' => 'menu-node2',
                'custom_options' => []
            ],
            'cms_block'
        );

        $item3 = $this->_getMenuItemMock(
            3,
            2,
            'http://some3.url',
            [
                'title' => 'Third level',
                'identifier' => 'menu-node3',
                'custom_options' => []
            ],
            'cms_block'
        );

        $item4 = $this->_getMenuItemMock(
            4,
            3,
            'http://some.url',
            [
                'title' => 'Fourth level',
                'identifier' => 'menu-node1',
                'custom_options' => []
            ],
            'custom_link'
        );

        $item5 = $this->_getMenuItemMock(
            5,
            3,
            'http://some.url',
            [
                'title' => 'Fourth level',
                'identifier' => 'menu-node1',
                'custom_options' => []
            ]
        );

        $item6 = $this->_getMenuItemMock(
            6,
            0,
            'http://some.url',
            [
                'title' => 'Highest Level  level',
                'identifier' => 'menu-node6',
                'custom_options' => []
            ]
        );

        $item7 = $this->_getMenuItemMock(45, 55, 'http://46.com', ['title' => '46 title'], 'category');

        $data1 = [$item1, $item2, $item3, $item4, $item5, $item6, $item7];
        $expected1 =
            new \Magento\Framework\DataObject(
                [
                    0 => [
                        'title' => 'Highest level',
                        'identifier' => 'menu-node1',
                        'custom_options' => [],
                        'url' => 'http://some.url',
                        'is_active' => false,
                        'children' => [
                            0 => [
                                'title' => 'Second level',
                                'identifier' => 'menu-node2',
                                'custom_options' => [],
                                'url' => 'http://some2.url',
                                'is_active' => false,
                                'children' => [
                                    0 => [
                                        'title' => 'Third level',
                                        'identifier' => 'menu-node3',
                                        'custom_options' => [],
                                        'url' => 'http://some3.url',
                                        'is_active' => false,
                                        'children' => [
                                            0 => [
                                                'title' => 'Fourth level',
                                                'identifier' => 'menu-node1',
                                                'custom_options' => [],
                                                'url' => 'http://some.url',
                                                'is_active' => false
                                            ],
                                            1 => [
                                                'title' => 'Fourth level',
                                                'identifier' => 'menu-node1',
                                                'custom_options' => [],
                                                'url' => 'http://some.url',
                                                'is_active' => false
                                            ]
                                        ]
                                    ]
                                ],
                            ]
                        ]
                    ],
                    1 => [
                        'title' => 'Highest Level  level',
                        'identifier' => 'menu-node6',
                        'custom_options' => [],
                        'url' => 'http://some.url',
                        'is_active' => false,
                    ]
                ]
            );
        return [
            [$data1, $expected1],
        ];
    }

    /**
     * Get Data
     * @param int $id
     * @param int $parentMenuItemId
     * @param string $url
     * @param [] $menuItemData
     * @param string $typeCode
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMenuItemMock(
        $id,
        $parentMenuItemId,
        $url,
        $menuItemData,
        $typeCode = 'custom_link'
    ) {
        $menuItem = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Model\Menu',
            [
                'getParentMenuItemId',
                'getId',
                'getUrl',
                'getMenuData',
                'getLink',
                'getTypeCode',
                'getCategoryId',
                'getActiveCategories',
                'isAvailable'
            ]
        );

        $helperMock = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Helper\Data',
            ['_getRequest', 'compareHandles']
        );

        $requestMock = $this->_getRequestMockWithoutConstructor(
            ['getFullActionName']
        );

        $helperMock->expects($this->any())
            ->method('_getRequest')
            ->willReturn($requestMock);

        $helperMock->expects($this->any())
            ->method('compareHandles')
            ->willReturn(false);

        $menuRef = $this->_createReflectionClass('Digidirect\Navigation\Model\Menu');
        $prop = $menuRef->getProperty('helper');
        $prop->setAccessible(true);
        $prop->setValue($menuItem, $helperMock);

        $categoryMock = $this->getMockObjectWithoutConstructor('Magento\Catalog\Model\Category');

        $menuItem->expects($this->any())
            ->method('getActiveCategories')
            ->willReturn([45 => $categoryMock, 60 => $categoryMock]);

        $menuItem->expects($this->any())
            ->method('getTypeCode')
            ->willReturn($typeCode);

        $menuItem->expects($this->any())
            ->method('getCategoryId')
            ->willReturn(45);

        $menuItem->expects($this->any())
            ->method('getId')
            ->willReturn($id);

        $menuItem->expects($this->any())
            ->method('getParentMenuItemId')
            ->willReturn($parentMenuItemId);

        $menuItem->expects($this->any())
            ->method('getUrl')
            ->willReturn($url);

        $menuItem->expects($this->any())
            ->method('getLink')
            ->willReturn($url);

        $menuItem->expects($this->any())
            ->method('getMenuData')
            ->willReturn($menuItemData);

        $menuItem->expects($this->any())
            ->method('isAvailable')
            ->willReturn(true);

        return $menuItem;
    }

    /**
     * Test without set
     */
    public function testGetMenuJsonWithoutSet()
    {
        $this->_menuBlock->setData('set_code', null);
        $this->assertEquals($this->_jsonHelperReal->jsonEncode([]), $this->_menuBlock->getMenuJson());
    }
}
