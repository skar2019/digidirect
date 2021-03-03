<?php
namespace Digidirect\Navigation\Test\Unit\Model;

use Digidirect\Navigation\Test\Unit\NavigationTestUnitTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class MenuTest extends \PHPUnit_Framework_TestCase
{
    use NavigationTestUnitTrait;

    /**
     * @var \Digidirect\Navigation\Model\Menu
     */
    protected $_menuModel;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * Setup required objects
     */
    public function setUp()
    {
        $this->_objectManager = new ObjectManager($this);
        $this->_menuModel = $this->_objectManager->getObject(
            'Digidirect\Navigation\Model\Menu',
            []
        );
    }

    /**
     * Test get url(actually test expected route parameters)
     *
     * @dataProvider provideLinks
     * @param [] $data
     * @param $urlParameters
     */
    public function testGetUrl($data, $urlParameters)
    {
        $link = $data['link'];
        $type = $data['type'];

        $helperMock = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Helper\Data',
            ['getMenuItemUrl']
        );

        $categoryTypeInstance = $this->_objectManager->getObject(
            'Digidirect\Navigation\Model\Menu\Type\Category',
            ['helper' => $helperMock]
        );

        $cmsBlockTypeInstance = $this->_objectManager->getObject(
            'Digidirect\Navigation\Model\Menu\Type\CmsBlock',
            ['helper' => $helperMock]
        );

        $customLinkTypeInstance = $this->_objectManager->getObject(
            'Digidirect\Navigation\Model\Menu\Type\CustomLinkAbstract',
            ['helper' => $helperMock]
        );

        $instancesMapper = [
            'category' => $categoryTypeInstance,
            'cms_block' => $cmsBlockTypeInstance,
            'custom_link' => $customLinkTypeInstance
        ];

        $poolMock = $this->getMockObjectWithoutConstructor('Digidirect\Navigation\Model\Menu\Type\Pool', ['get']);

        $poolMock->expects($this->any())
            ->method('get')
            ->willReturn($instancesMapper[$type]);

        $menuModel = $this->_objectManager->getObject(
            'Digidirect\Navigation\Model\Menu',
            [
                'data' => [
                    'link' => $link,
                    'type_code' => $type,
                    'type_instance' => [
                        'category' => 'Digidirect\Navigation\Model\Menu\Type\Category',
                        'cms_block' => 'Digidirect\Navigation\Model\Menu\Type\CmsBlock',
                        'custom_link' => 'Digidirect\Navigation\Model\Menu\Type\CustomLinkAbstract'
                    ]
                ],
                'helper' => $helperMock,
                'pool' => $poolMock
            ]
        );

        $expectedRoute = $urlParameters[0];
        $expectedParameter = $urlParameters[1];

        $helperMock->expects($this->any())
            ->method('getMenuItemUrl')
            ->with($expectedRoute, $expectedParameter)
            ->willReturn('http://baseUrl/' . $expectedRoute);

        $this->assertEquals('http://baseUrl/' . $expectedRoute, $menuModel->getUrl());
    }

    /**
     * @return array
     */
    public function provideLinks()
    {
        return [
            [
                [
                    'type' => 'custom_link',
                    'link' => '{{secure_base_url}}/store_secure'
                ],
                [
                    'store_secure',
                    [
                        '_secure' => true
                    ]
                ]
            ],
            [
                ['link' => '{{unsecure_base_url}}/store_unsecure', 'type' => 'custom_link'],
                [
                    'store_unsecure',
                    [
                        '_secure' => false
                    ]
                ]
            ],
            [
                [
                    'link' => '{{base_url}}/store_base',
                    'type' => 'custom_link'
                ],
                [
                    'store_base',
                    [
                        '_secure' => true
                    ]
                ]
            ],
            [
                [
                    'link' => 'catalog/product/view',
                    'type' => 'custom_link'
                ],
                [
                    'catalog/product/view',
                    [
                        '_secure' => false
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderValidate
     * @param $link
     * @param $expected
     */
    public function testValidate($link, $expected)
    {
        $menuModel = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Model\Menu',
            ['_getResource']
        );

        $menuModelReflection = $this->_createReflectionClass('Digidirect\Navigation\Model\Menu');

        $property = $this->setAccessibleProperty($menuModelReflection, 'pool');

        $dataProperty = $this->setAccessibleProperty($menuModelReflection, '_data');
        $dataProperty->setValue(
            $menuModel,
            ['type_instance' => ['custom_link' => 'Digidirect\Navigation\Model\Menu\Type\CustomLinkAbstract']]
        );

        $poolMock = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Model\Menu\Type\Pool',
            ['get']
        );
        $property->setValue($menuModel, $poolMock);

        $customLinkMock = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Model\Menu\Type\CustomLinkAbstract',
            ['getMenuData']
        );
        $poolMock->expects($this->any())
            ->method('get')
            ->willReturn($customLinkMock);

        $resourceMock = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Model\ResourceModel\Menu',
            ['validate']
        );
        $menuModel->expects($this->any())
            ->method('_getResource')
            ->willReturn($resourceMock);

        $resourceMock->expects($this->any())
            ->method('validate')
            ->willReturn([]);

        $requestMock = $this->_getRequestMock();
        $requestMock->expects($this->any())
            ->method('getParam')
            ->with('link')
            ->willReturn($link);

        $result = $menuModel->validate($requestMock, 0, 1);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getRequestMock()
    {
        return $this->getMockObjectWithoutConstructor(
            'Magento\Framework\App\Request\Http',
            ['getParam']
        );
    }

    /**
     * @return array
     */
    public function dataProviderValidate()
    {
        return [
            [
                'link',[]
            ],
            ['{{unsecure_base_url}}', []],
            ['http://test.com/', []],
            ['', []]
        ];
    }

    /**
     * Test adding/fetching default data(using for "Use Default")
     *
     * @return void
     */
    public function testAddDefaultData()
    {
        $data = ['field' => 'value', 'field2' => 'value2'];
        $this->_menuModel->addDataDefault($data);
        $this->_menuModel->addDataDefault(['field2' => 'newFiled2']);

        $this->assertEquals(['field' => 'value', 'field2' => 'newFiled2'], $this->_menuModel->getDataDefault());
    }

    /**
     * Test get menu data by type
     *
     * @param $data
     * @param $expected
     * @dataProvider provideMenuData
     */
    public function testGetMenuData($data, $expected)
    {
        $cmsBlockFactory = $this->getMockObjectWithoutConstructor('Magento\Cms\Block\BlockFactory', ['create']);

        $cmsBlock = $this->getMockObjectWithoutConstructor('Magento\Cms\Block\Block', ['setBlockId', 'toHtml']);
        $cmsBlock->expects($this->any())
            ->method('setBlockId')
            ->willReturnSelf();

        $cmsBlock->expects($this->any())
            ->method('toHtml')
            ->willReturn('Cms Block Content');

        $cmsBlockFactory->expects($this->any())
            ->method('create')
            ->willReturn($cmsBlock);

        $poolMock = $this->getMockObjectWithoutConstructor('Digidirect\Navigation\Model\Menu\Type\Pool', ['get']);

        $categoryTypeInstance = $this->_objectManager->getObject(
            'Digidirect\Navigation\Model\Menu\Type\Category'
        );

        $cmsBlockTypeInstance = $this->_objectManager->getObject(
            'Digidirect\Navigation\Model\Menu\Type\CmsBlock',
            ['blockFactory' => $cmsBlockFactory]
        );

        $customLinkTypeInstance = $this->_objectManager->getObject(
            'Digidirect\Navigation\Model\Menu\Type\CustomLinkAbstract'
        );

        $instancesMapper = [
            'category' => $categoryTypeInstance,
            'cms_block' => $cmsBlockTypeInstance,
            'custom_link' => $customLinkTypeInstance
        ];

        $poolMock->expects($this->any())
            ->method('get')
            ->willReturn($instancesMapper[$data['type_code']]);

        $filterProviderMock = $this->getMockObjectWithoutConstructor(
            \Magento\Cms\Model\Template\FilterProvider::class,
            ['getPageFilter']
        );

        $pageFilterMock = $this->getMockObjectWithoutConstructor(
            \Magento\Framework\Filter\Template::class,
            ['filter']
        );

        $pageFilterMock->expects($this->any())
            ->method('filter')
            ->willReturn(false);

        $filterProviderMock->expects($this->any())
            ->method('getPageFilter')
            ->willReturn($pageFilterMock);

        $arguments = $this->_objectManager->getConstructArguments(
            'Digidirect\Navigation\Model\Menu',
            [
                'data' => [
                    'type_instance' => [
                        'category' => 'Digidirect\Navigation\Model\Menu\Type\Category',
                        'cms_block' => 'Digidirect\Navigation\Model\Menu\Type\CmsBlock',
                        'custom_link' => 'Digidirect\Navigation\Model\Menu\Type\CustomLinkAbstract'
                    ]
                ],
                'pool' => $poolMock,
                'filterProvider' => $filterProviderMock
            ]
        );

        $menuModelMock = $this->getMockObjectWithConstructor(
            'Digidirect\Navigation\Model\Menu',
            [
                'getTypeCode',
                'getId',
                'getMenuItemIsLink',
                'getTitle',
                'getCustomOptions',
                'getCmsBlockId'
            ],
            $arguments
        );

        $menuModelMock->expects($this->any())
            ->method('getTypeCode')
            ->willReturn($data['type_code']);

        $menuModelMock->expects($this->any())
            ->method('getId')
            ->willReturn($data['identifier']);

        $menuModelMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($data['title']);

        $menuModelMock->expects($this->any())
            ->method('getCustomOptions')
            ->willReturn($data['custom_options']);

        $menuModelMock->expects($this->any())
            ->method('getMenuItemIsLink')
            ->willReturn($data['is_link']);

        $menuModelMock->expects($this->any())
            ->method('getCmsBlockId')
            ->willReturn($data['cms_block_id'] ?? null);

        $this->assertEquals($expected, $menuModelMock->getMenuData());
    }

    /**
     * Provide menu types
     *
     * @return []
     */
    public function provideMenuData()
    {
        $data1 = [
            'title' => 'Title 1',
            'identifier' => 2,
            'custom_options' => serialize(['1' => '1']),
            'type_code' => 'cms_block',
            'is_link' => true,
            'cms_block_id' => 852,
            'position' => null,
            'menu_item_type' => 'cms_block',
            'frontend_class' => '',
            'style' => ''
        ];

        $expected1 = [
            'title' => 'Title 1',
            'identifier' => 'menu-node2',
            'custom_options' => ['1' => '1'],
            'cms_block_content' => 'Cms Block Content',
            'is_link' => true,
            'position' => null,
            'menu_item_type' => 'cms_block',
            'content' => false,
            'frontend_class' => '',
            'style' => ''
        ];

        $data2 = [
            'title' => 'Title 126',
            'identifier' => 126,
            'custom_options' => null,
            'type_code' => 'custom_link',
            'is_link' => true,
            'position' => null,
            'menu_item_type' => 'cms_block',
        ];

        $expected2 = [
            'title' => 'Title 126',
            'identifier' => 'menu-node126',
            'custom_options' => [],
            'is_link' => false,
            'position' => null,
            'menu_item_type' => 'custom_link',
            'content' => false,
            'frontend_class' => '',
            'style' => ''
        ];

        $data3 = [
            'title' => 'Title 3',
            'identifier' => 45,
            'custom_options' => null,
            'type_code' => 'category',
            'is_link' => true,
        ];

        $expected3 = [
            'title' => 'Title 3',
            'identifier' => 'menu-node45',
            'custom_options' => [],
            'is_link' => true,
            'position' => null,
            'menu_item_type' => 'category',
            'content' => false,
            'frontend_class' => '',
            'style' => ''
        ];

        return [
            [$data1, $expected1],
            [$data2, $expected2],
            [$data3, $expected3]
        ];
    }
}
