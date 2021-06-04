<?php
namespace Ewave\CmsUpgrade\Helper;

use Ewave\CmsUpgrade\Test\Unit\CmsUpgradeTestUnitTrait;
use Ewave\CmsUpgrade\Helper\MapperWidget;
use Magento\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Banner\Model\ResourceModel\Banner\Collection;

class MapperWidgetTest extends \PHPUnit_Framework_TestCase
{
    use CmsUpgradeTestUnitTrait;

    /**
     * MapperWidget object
     * @var MapperWidget | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mapperWidgetMock;

    /**
     * MapperWidget Object reflection
     * @var ReflectionClass
     */
    protected $_mapperWidgetReflector;

    /**
     * Collection object
     * @var Collection | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionMock;

    /**
     * CollectionFactory object
     * @var CollectionFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactoryMock;

    /**
     * @var array
     */
    protected $_bannerNames = [
        'bannerName1',
        'bannerName2',
    ];

    /**
     * @var string
     */
    protected $_bannerIds = '3,5';

    /**
     * Set up reused objects
     * @return void
     */
    public function setUp()
    {
        $this->_mapperWidgetMock = $this->getMockObjectWithoutConstructor(
            MapperWidget::class,
            ['orderByField']
        );
        $this->_mapperWidgetReflector = $this->_createReflectionClass(MapperWidget::class);

        $this->_collectionFactoryMock = $this->getMockObjectWithoutConstructor(
            CollectionFactory::class
        );

        $this->_collectionMock = $this->getMockObjectWithoutConstructor(
            Collection::class,
            []
        );

        $this->_collectionFactoryMock = $this->getMockObjectWithoutConstructor(
            CollectionFactory::class,
            ['create']
        );
        $this->_collectionFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->_collectionMock);

        $bannerProperty = $this->setAccessibleProperty($this->_mapperWidgetReflector, '_bannerCollectionFactory');
        $bannerProperty->setValue(
            $this->_mapperWidgetMock,
            $this->_collectionFactoryMock
        );

        $this->_collectionMock->expects($this->any())
            ->method('addFieldToSelect')
            ->willReturnSelf();

        $this->_collectionMock->expects($this->any())
            ->method('addFieldToFilter')
            ->willReturnSelf();

        $this->_collectionMock->expects($this->any())
            ->method('getSize')
            ->willReturn(2);
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider provideWidgetParams
     */
    public function testProcessMagentoBanner($data, $expected)
    {
        $this->_collectionMock->expects($this->any())
            ->method('getColumnValues')
            ->willReturn($this->_bannerNames);

        $this->_mapperWidgetMock->expects($this->any())
            ->method('orderByField')
            ->willReturn($this->_collectionMock);

        $method = $this->_mapperWidgetReflector->getMethod('processMagentoBanner');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invokeArgs($this->_mapperWidgetMock, [$data]));
    }

    /**
     * @return array
     */
    public function provideWidgetParams()
    {
        return [
            [
                ['array_without_page_group' => ''],
                [
                    'array_without_page_group' => ''
                ]
            ],
            [
                [

                    'display_mode' => 'fixed',
                    'types' =>
                        [
                            0 => 'content',
                        ],
                    'rotate' => 'random',
                    'banner_ids' => '3,5',
                    'unique_id' => '0403960497655be7d45a8edbf59c89b1',
                    'rotation_speed' => '12',
                    'autoplay' => '1',
                    'loop' => '1',
                    'breadcrumbs_type' => 'bullets',
                    'custom_template' => '122',

                ],
                [

                    'display_mode' => 'fixed',
                    'types' =>
                        [
                            0 => 'content',
                        ],
                    'rotate' => 'random',
                    'banner_ids' => '3,5',
                    'unique_id' => '0403960497655be7d45a8edbf59c89b1',
                    'rotation_speed' => '12',
                    'autoplay' => '1',
                    'loop' => '1',
                    'breadcrumbs_type' => 'bullets',
                    'custom_template' => '122',
                    'banner_uniqs' => $this->_bannerNames

                ]


            ]
        ];
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider provideWidgetParamsPost
     */
    public function testPostMagentoBanner($data, $expected)
    {
        $this->_collectionMock->expects($this->any())
            ->method('getColumnValues')
            ->willReturn(explode(',', $this->_bannerIds));

        $this->_mapperWidgetMock->expects($this->any())
            ->method('orderByField')
            ->willReturn($this->_collectionMock);

        $method = $this->_mapperWidgetReflector->getMethod('postMagentoBanner');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invokeArgs($this->_mapperWidgetMock, [$data]));
    }

    /**
     * @return array
     */
    public function provideWidgetParamsPost()
    {
        return [
            [
                ['array_without_page_group' => ''],
                [
                    'array_without_page_group' => ''
                ]
            ],
            [
                [

                    'display_mode' => 'fixed',
                    'types' =>
                        [
                            0 => 'content',
                        ],
                    'rotate' => 'random',
                    'unique_id' => '0403960497655be7d45a8edbf59c89b1',
                    'rotation_speed' => '12',
                    'autoplay' => '1',
                    'loop' => '1',
                    'breadcrumbs_type' => 'bullets',
                    'custom_template' => '122',
                    'banner_uniqs' => $this->_bannerNames

                ],
                [

                    'display_mode' => 'fixed',
                    'types' =>
                        [
                            0 => 'content',
                        ],
                    'rotate' => 'random',
                    'banner_ids' => $this->_bannerIds,
                    'unique_id' => '0403960497655be7d45a8edbf59c89b1',
                    'rotation_speed' => '12',
                    'autoplay' => '1',
                    'loop' => '1',
                    'breadcrumbs_type' => 'bullets',
                    'custom_template' => '122',
                    'banner_uniqs' => $this->_bannerNames
                ]
            ]
        ];
    }

    /**
     * @param $methodName
     * @param $data
     * @param $expected
     * @dataProvider provideWidgetParamsCall
     */
    public function testMagicCall($methodName, $data, $expected)
    {
        $method = $this->_mapperWidgetReflector->getMethod('__call');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invokeArgs($this->_mapperWidgetMock, [$methodName, $data]));
    }

    /**
     * @return array
     */
    public function provideWidgetParamsCall()
    {
        return [
            [
                'method',
                ['array_without_page_group' => 'test'],
                'test'
            ],
            [
                'method2',
                [
                    'display_mode' => 'fixed',
                    'types' =>
                        [
                            0 => 'content',
                        ],
                    'rotate' => 'random',
                    'unique_id' => '0403960497655be7d45a8edbf59c89b1',
                    'rotation_speed' => '12',
                    'autoplay' => '1',
                    'loop' => '1',
                    'breadcrumbs_type' => 'bullets',
                    'custom_template' => '122'

                ],
                'fixed'
            ]
        ];
    }

    /**
     * @param $step
     * @param $dataWidget
     * @param $widgetTypeClass
     * @param $expected
     * @dataProvider provideRunProcessor
     */
    public function testRunProcessor($step, $dataWidget, $widgetTypeClass, $expected)
    {
        $mapperWidgetMockRunProcess = $this->getMockObjectWithoutConstructor(
            MapperWidget::class,
            ['processMagentoBanner', 'postMagentoBanner']
        );

        $mapperWidgetMockRunProcess->expects($this->any())
            ->method('processMagentoBanner')
            ->willReturn('runProcessMagentoBanner');

        $mapperWidgetMockRunProcess->expects($this->any())
            ->method('postMagentoBanner')
            ->willReturn('runPostMagentoBanner');

        $this->assertEquals($expected, $mapperWidgetMockRunProcess->runProcessor($step, $dataWidget, $widgetTypeClass));
    }

    /**
     * @return array
     */
    public function provideRunProcessor()
    {
        return [
            [
                'process',
                ['dataWidget'],
                'Magento\Banner\Block\Widget\Banner',
                'runProcessMagentoBanner',
            ],
            [
                'post',
                ['dataWidget'],
                'Magento\Banner\Block\Widget\Banner',
                'runPostMagentoBanner',
            ],
            [
                'post',
                ['dataWidget'],
                'Magento\Banner\Block\Widget\BannerFake',
                ['dataWidget'],
            ]
        ];
    }
}