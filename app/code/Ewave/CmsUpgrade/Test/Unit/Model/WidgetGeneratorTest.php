<?php
namespace Ewave\CmsUpgrade\Test\Unit\Model;

use Ewave\CmsUpgrade\Test\Unit\CmsUpgradeTestUnitTrait;
use Ewave\CmsUpgrade\Model\WidgetGenerator;
use Ewave\CmsUpgrade\Model\Entity\AbstractEntity as Entity;
use Magento\Widget\Model\Widget\Instance as Widget;
use Ewave\CmsUpgrade\Helper\Data;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class WidgetGeneratorTest
 * @package Ewave\CmsUpgrade\Test\Unit\Model
 */
class WidgetGeneratorTest extends \PHPUnit_Framework_TestCase
{
    use CmsUpgradeTestUnitTrait;

    /**
     * WidgetGenerator object
     * @var WidgetGenerator | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_widgetGeneratorMock;

    /**
     * WidgetGenerator Object reflection
     * @var ReflectionClass
     */
    protected $_widgetGeneratorReflector;

    /**
     * Widget object
     *
     * @var Widget | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_widgetModelMock;

    /**
     * Widget Object reflection
     * @var ReflectionClass
     */
    protected $_widgetModelReflector;

    /**
     * @var array
     */
    protected $_entityFields = [
        'instance_type',
        'theme_id',
        'title',
        'store_ids',
        'widget_parameters',
        'sort_order',
        'store_contents',
        'instance_code',
        'page_groups',
        'page_group_ids',
    ];

    /**
     * Set up reused objects
     * @return void
     */
    public function setUp()
    {
        $this->_widgetGeneratorMock = $this->getMockObjectWithoutConstructor(
            WidgetGenerator::class,
            null
        );
        $this->_widgetGeneratorReflector = $this->_createReflectionClass(WidgetGenerator::class);

        $this->_widgetModelMock = $this->getMockObjectWithoutConstructor(
            Widget::class,
            null
        );
        $this->_widgetModelReflector = $this->_createReflectionClass(Widget::class);

        $eventManager = $this->getMockObjectWithoutConstructor(
            ManagerInterface::class,
            ['dispatch']
        );

        $helper = $this->getMockObjectWithoutConstructor(
            Data::class,
            null
        );
        $helperProperty = $this->setAccessibleProperty($this->_widgetGeneratorReflector, '_helper');
        $helperProperty->setValue(
            $this->_widgetGeneratorMock,
            $helper
        );

        $eventManagerProperty = $this->setAccessibleProperty($this->_widgetGeneratorReflector, '_eventManager');
        $eventManagerProperty->setValue(
            $this->_widgetGeneratorMock,
            $eventManager
        );

        $generateEntity = $this->getMockObjectWithoutConstructor(
            Entity::class,
            ['getUpgradeFields']
        );

        $generateEntity->expects($this->any())
            ->method('getUpgradeFields')
            ->willReturn(
                $this->_entityFields
            );

        $generateEntityReflector = $this->_createReflectionClass(Entity::class);

        $generateEntityProperty = $this->setAccessibleProperty($this->_widgetGeneratorReflector, '_generateEntity');
        $generateEntityProperty->setValue(
            $this->_widgetGeneratorMock,
            $generateEntity
        );

        $upgradeFieldsProperty = $this->setAccessibleProperty($generateEntityReflector, '_upgradeFields');
        $upgradeFieldsProperty->setValue(
            $generateEntity,
            $this->_entityFields
        );
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider provideWidgetFillData
     */
    public function testFillWidgetData($data, $expected)
    {
        $dataProperty = $this->_widgetModelReflector->getProperty('_data');
        $dataProperty->setAccessible(true);
        $dataProperty->setValue($this->_widgetModelMock, $data);

        $method = $this->_widgetGeneratorReflector->getMethod('_fillData');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invokeArgs($this->_widgetGeneratorMock, [$this->_widgetModelMock]));
    }

    /**
     * @return array
     */
    public function provideWidgetFillData()
    {
        return [
            [
                ['array_without_page_group' => ''],
                [
                    'instance_type' => null,
                    'theme_id' => null,
                    'title' => null,
                    'store_ids' => null,
                    'widget_parameters' => [],
                    'sort_order' => null,
                    'store_contents' => null,
                    'instance_code' => null,
                    'page_groups' => null,
                    'page_group_ids' => null,
                ]
            ],
            [
                [
                    'instance_type' => 'Magento\\Banner\\Block\\Widget\\Banner',
                    'theme_id' => '4',
                    'title' => 'titleMynewWidget',
                    'store_ids' =>
                        [
                            0 => '0',
                        ],
                    'widget_parameters' =>
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
                    'sort_order' => '2123',
                    'store_contents' => null,
                    'instance_code' => null,
                    'page_groups' =>
                        [
                            0 =>
                                [
                                    'page_id' => '47',
                                    'instance_id' => '41',
                                    'page_group' => 'all_pages',
                                    'layout_handle' => 'default',
                                    'block_reference' => 'header-wrapper',
                                    'page_for' => 'all',
                                    'entities' => '',
                                    'page_template' => 'widget/block.phtml',
                                ],
                            1 =>
                                [
                                    'page_id' => '48',
                                    'instance_id' => '41',
                                    'page_group' => 'pages',
                                    'layout_handle' => 'cms_index_index',
                                    'block_reference' => 'minicart.addons',
                                    'page_for' => 'all',
                                    'entities' => '',
                                    'page_template' => 'widget/inline.phtml',
                                ],
                        ],
                    'page_group_ids' => null,
                ],
                [
                    'instance_type' => 'Magento\\Banner\\Block\\Widget\\Banner',
                    'theme_id' => '4',
                    'title' => 'titleMynewWidget',
                    'store_ids' =>
                        [
                            0 => '0',
                        ],
                    'widget_parameters' =>
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
                    'sort_order' => '2123',
                    'store_contents' => null,
                    'instance_code' => null,
                    'page_groups' =>
                        [
                            0 =>
                                [
                                    'page_id' => '47',
                                    'instance_id' => '41',
                                    'page_group' => 'all_pages',
                                    'layout_handle' => 'default',
                                    'block_reference' => 'header-wrapper',
                                    'page_for' => 'all',
                                    'entities' => '',
                                    'page_template' => 'widget/block.phtml',
                                ],
                            1 =>
                                [
                                    'page_id' => '48',
                                    'instance_id' => '41',
                                    'page_group' => 'pages',
                                    'layout_handle' => 'cms_index_index',
                                    'block_reference' => 'minicart.addons',
                                    'page_for' => 'all',
                                    'entities' => '',
                                    'page_template' => 'widget/inline.phtml',
                                ],
                        ],
                    'page_group_ids' => null,
                ]
            ]
        ];
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider provideWidgetGroups
     */
    public function testPrepareWidgetGroup($data, $expected)
    {
        $method = $this->_widgetGeneratorReflector->getMethod('_prepareWidgetGroup');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invokeArgs($this->_widgetGeneratorMock, [$data]));
    }

    /**
     * @return array
     */
    public function provideWidgetGroups()
    {
        return [
            [
                ['array_without_page_group' => ''],
                ['array_without_page_group' => '']
            ],
            [
                [
                    'instance_type' => 'Magento\\Banner\\Block\\Widget\\Banner',
                    'theme_id' => '4',
                    'title' => 'titleMynewWidget',
                    'store_ids' =>
                        [
                            0 => '0',
                        ],
                    'widget_parameters' =>
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
                    'sort_order' => '2123',
                    'store_contents' => null,
                    'instance_code' => null,
                    'page_groups' =>
                        [
                            0 =>
                                [
                                    'page_id' => '47',
                                    'instance_id' => '41',
                                    'page_group' => 'all_pages',
                                    'layout_handle' => 'default',
                                    'block_reference' => 'header-wrapper',
                                    'page_for' => 'all',
                                    'entities' => '',
                                    'page_template' => 'widget/block.phtml',
                                ],
                            1 =>
                                [
                                    'page_id' => '48',
                                    'instance_id' => '41',
                                    'page_group' => 'pages',
                                    'layout_handle' => 'cms_index_index',
                                    'block_reference' => 'minicart.addons',
                                    'page_for' => 'all',
                                    'entities' => '',
                                    'page_template' => 'widget/inline.phtml',
                                ],
                        ],
                    'page_group_ids' => null,
                ],
                [
                    'instance_type' => 'Magento\\Banner\\Block\\Widget\\Banner',
                    'theme_id' => '4',
                    'title' => 'titleMynewWidget',
                    'store_ids' =>
                        [
                            0 => '0',
                        ],
                    'widget_parameters' =>
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
                    'sort_order' => '2123',
                    'store_contents' => null,
                    'instance_code' => null,
                    'page_groups' =>
                        [
                            0 =>
                                [
                                    'page_group' => 'all_pages',
                                    'all_pages' =>
                                        [
                                            'page_id' => '47',
                                            'layout_handle' => 'default',
                                            'block' => 'header-wrapper',
                                            'for' => 'all',
                                            'entities' => '',
                                            'template' => 'widget/block.phtml',
                                        ],
                                ],
                            1 =>
                                [
                                    'page_group' => 'pages',
                                    'pages' =>
                                        [
                                            'page_id' => '48',
                                            'layout_handle' => 'cms_index_index',
                                            'block' => 'minicart.addons',
                                            'for' => 'all',
                                            'entities' => '',
                                            'template' => 'widget/inline.phtml',
                                        ],
                                ],
                        ],
                    'page_group_ids' => null,
                ]


            ]
        ];
    }
}
