<?php
namespace Ewave\CmsUpgrade\Test\Unit\Model;

use Ewave\CmsUpgrade\Test\Unit\CmsUpgradeTestUnitTrait;
use Ewave\CmsUpgrade\Helper\Data;
use Ewave\CmsUpgrade\Model\BannerGenerator;
use Ewave\CmsUpgrade\Model\Entity\Banner as EntityBanner;
use InvalidArgumentException;
use Magento\Banner\Model\Banner;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use ReflectionClass;

/**
 * Class BannerGeneratorTest
 * @package Ewave\CmsUpgrade\Test\Unit\Model
 */
class BannerGeneratorTest extends \PHPUnit_Framework_TestCase
{
    use CmsUpgradeTestUnitTrait;

    /**
     * BannerGenerator object
     * @var BannerGenerator | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_bannerGeneratorMock;

    /**
     * BannerGenerator Object reflection
     * @var ReflectionClass
     */
    protected $_bannerGeneratorReflector;

    /**
     * Banner object
     *
     * @var Banner | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_bannerModelMock;

    /**
     * Banner Object reflection
     * @var ReflectionClass
     */
    protected $_bannerModelReflector;

    /**
     * @var array
     */
    protected $_bannerData = [
        1 => [
            'name' => 'testBannerVideo',
            'is_enabled' => '1',
            'types' =>
                [
                    0 => 'footer',
                ],
            'is_ga_enabled' => '0',
            'ga_creative' => null,
            'customer_segment_ids' => [],
            'store_contents' => [],
            'custom_attributes' =>
                [
                    'entity_id' => '42',
                    'banner_id' => '42',
                    'images' => 'a:0:{}',
                    'navigation_title' => 'TitleNavigation',
                    'target_type' => 'product',
                    'target_id' => '256',
                    'alt' => 'testBannerVideoFinal',
                    'navigation_title_type' => '1',
                    'navigation_image' => 'banners/navigation_image21/log12313131o_3.png',
                ],
            'images_banner' =>
                [
                    0 =>
                        [
                            'file' => 'banners/w0ngdqn2qae9w4aitp/preview/log12313131o.png',
                            'video_file' => 'banners/w0ngdqn2qae9w4aitp/small.mp4',
                            'video_title' => 'testVideo',
                            'value_id' => '67',
                            'type' => '',
                            'media_type' => 'external-video',
                            'role_code' => 'Desktop, Mobile, Tablet',
                            'allow_video_popup' => '0',
                            'play_video_in_a_loop' => '0',
                            'play_video_after' => '0',
                            'play_video_automatically_for_mobile' => '0',
                            'play_video_automatically_for_desktop' => '0',
                            'video_roles' =>
                                [
                                    0 => 'rotator_desktop',
                                    1 => 'rotator_mobile',
                                    2 => 'rotator_tablet',
                                ],
                            'use_video_as_a_preview' => '1',
                            'video_folder' => 'w0ngdqn2qae9w4aitp',
                            'is_video_as_preview' => false,
                        ],
                ],
        ],
        2 => [
            'banner_id' => '37',
            'name' => 'testImageBanner',
            'is_enabled' => '1',
            'types' => null,
            'store_contents' => [],
            'is_ga_enabled' => '0',
            'ga_creative' => null,
            'custom_attributes' =>
                [
                    'entity_id' => '37',
                    'banner_id' => '37',
                    'images' => 'a:2:{s:18:"2l1fer3q67vs7sum5b";s:43:"banners/2l1fer3q67vs7sum5b/log12313131o.png";s:18:"qklnlos02orec4jfdn";s:39:"banners/qklnlos02orec4jfdn/images_2.jpg";}',
                    'navigation_title' => '',
                    'target_type' => '0',
                    'target_id' => null,
                    'alt' => 'testImageBanner',
                    'navigation_title_type' => '1',
                    'navigation_image' => null,
                ],
            'entity_id' => '37',
            'images' => 'a:2:{s:18:"2l1fer3q67vs7sum5b";s:43:"banners/2l1fer3q67vs7sum5b/log12313131o.png";s:18:"qklnlos02orec4jfdn";s:39:"banners/qklnlos02orec4jfdn/images_2.jpg";}',
            'navigation_title' => '',
            'target_type' => '0',
            'target_id' => null,
            'alt' => 'testImageBanner',
            'navigation_title_type' => '1',
            'navigation_image' => null,
            'images_banner' =>
                [
                    '2l1fer3q67vs7sum5b' => [
                        'file' => 'banners/2l1fer3q67vs7sum5b/log12313131o.png',
                        'type' => '',
                        'value_id' => '2l1fer3q67vs7sum5b',
                        'role_code' => '',
                    ],
                    'qklnlos02orec4jfdn' => [
                        'file' => 'banners/qklnlos02orec4jfdn/images_2.jpg',
                        'type' => '',
                        'value_id' => 'qklnlos02orec4jfdn',
                        'role_code' => '',
                    ],
                ],
            'customer_segment_ids' => []
        ],
        3 => [
            'banner_id' => '38',
            'name' => 'testContent',
            'is_enabled' => '1',
            'store_contents' => [
                'store_contents'
            ],
            'types' => null,
            'is_ga_enabled' => '0',
            'ga_creative' => null,
            'custom_attributes' =>
                [
                    'entity_id' => '38',
                    'banner_id' => '38',
                    'images' => 'a:0:{}',
                    'navigation_title' => '',
                    'target_type' => 'custom_link',
                    'target_id' => '/test.html',
                    'alt' => 'testBanner',
                    'navigation_title_type' => '1',
                    'navigation_image' => null,
                ],
            'entity_id' => '38',
            'images' => 'a:0:{}',
            'navigation_title' => '',
            'target_type' => 'custom_link',
            'target_id' => '/test.html',
            'alt' => 'testBanner',
            'navigation_title_type' => '1',
            'navigation_image' => null,
            'images_banner' => [],
            'customer_segment_ids' => [],
            'store_contents' => [
                'store_contents'
            ]
        ],
    ];

    /**
     * @var array
     */
    protected $_bannerDataOut = [
        1 => [
            'entityType' => 'banner',
            'items' =>
                [
                    0 =>
                        [
                            'name' => 'testBannerVideo',
                            'is_enabled' => '1',
                            'types' => [
                                0 => 'footer',
                            ],
                            'is_ga_enabled' => '0',
                            'ga_creative' => null,
                            'customer_segment_ids' => [],
                            'store_contents' => [],
                            'entity_id' => '42',
                            'images' => 'a:0:{}',
                            'navigation_title' => 'TitleNavigation',
                            'target_type' => 'product',
                            'product_id' => 'product/256',
                            'product' => 'product/256',
                            'target_id' => 'product/256',
                            'alt' => 'testBannerVideoFinal',
                            'navigation_title_type' => '1',
                            'navigation_image' => 'banners/navigation_image21/log12313131o_3.png',
                            'uploaded_videos' =>
                                [
                                    'w0ngdqn2qae9w4aitp' => [
                                        'file' => 'banners/w0ngdqn2qae9w4aitp/preview/log12313131o.png',
                                        'video_file' => 'banners/w0ngdqn2qae9w4aitp/small.mp4',
                                        'video_title' => 'testVideo',
                                        'type' => '',
                                        'media_type' => 'external-video',
                                        'role_code' => 'Desktop, Mobile, Tablet',
                                        'allow_video_popup' => '0',
                                        'play_video_in_a_loop' => '0',
                                        'play_video_after' => '0',
                                        'play_video_automatically_for_mobile' => '0',
                                        'play_video_automatically_for_desktop' => '0',
                                        'video_roles' => 'rotator_desktop,rotator_mobile,rotator_tablet',
                                        'use_video_as_a_preview' => '1',
                                        'video_folder' => 'w0ngdqn2qae9w4aitp',
                                        'is_video_as_preview' => false,
                                        'preview_image' => 'banners/w0ngdqn2qae9w4aitp/preview/log12313131o.png',
                                        'path' => 'banners/w0ngdqn2qae9w4aitp/small.mp4',
                                    ],
                                ],
                        ]
                ]
        ],
        2 => [
            'entityType' => 'banner',
            'items' =>
                [
                    0 =>
                        [
                            'name' => 'testImageBanner',
                            'is_enabled' => '1',
                            'types' => [],
                            'is_ga_enabled' => '0',
                            'ga_creative' => null,
                            'customer_segment_ids' => [],
                            'store_contents' => [],
                            'entity_id' => '37',
                            'images' => 'a:2:{s:18:"2l1fer3q67vs7sum5b";s:43:"banners/2l1fer3q67vs7sum5b/log12313131o.png";s:18:"qklnlos02orec4jfdn";s:39:"banners/qklnlos02orec4jfdn/images_2.jpg";}',
                            'navigation_title' => '',
                            'target_type' => '0',
                            'target_id' => null,
                            'alt' => 'testImageBanner',
                            'navigation_title_type' => '1',
                            'navigation_image' => null,
                        ]
                ]


        ],
        3 => [
            'entityType' => 'banner',
            'items' =>
                [
                    0 =>
                        [
                            'name' => 'testContent',
                            'is_enabled' => '1',
                            'types' => [],
                            'is_ga_enabled' => '0',
                            'ga_creative' => null,
                            'customer_segment_ids' => [],
                            'store_contents' =>
                                [
                                    0 => 'store_contents',
                                ],
                            'entity_id' => '38',
                            'images' => 'a:0:{}',
                            'navigation_title' => '',
                            'target_type' => 'custom_link',
                            'custom_link_id' => '/test.html',
                            'custom_link' => '/test.html',
                            'target_id' => '/test.html',
                            'alt' => 'testBanner',
                            'navigation_title_type' => '1',
                            'navigation_image' => null,
                            'store_contents_not_use' =>
                                [
                                    1 => '1',
                                    2 => '2',
                                ],
                        ],
                ],
        ]
    ];

    /**
     * @var array
     */
    protected $_entityFields = [
        'name',
        'is_enabled',
        'types',
        'is_ga_enabled',
        'ga_creative',
        'customer_segment_ids',
        'store_contents',
        'custom_attributes',
        'images_banner',
    ];

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManger;

    /**
     * Set up reused objects
     * @return void
     */
    public function setUp()
    {
        $this->_bannerGeneratorMock = $this->getMockObjectWithoutConstructor(
            BannerGenerator::class,
            null
        );
        $this->_bannerGeneratorReflector = $this->_createReflectionClass(BannerGenerator::class);

        $this->_bannerModelMock = $this->getMockObjectWithoutConstructor(
            Banner::class,
            ['load']
        );
        $this->_bannerModelReflector = $this->_createReflectionClass(Banner::class);

        $bannerProperty = $this->setAccessibleProperty($this->_bannerGeneratorReflector, '_banner');
        $bannerProperty->setValue(
            $this->_bannerGeneratorMock,
            $this->_bannerModelMock
        );

        $helper = $this->getMockObjectWithoutConstructor(
            Data::class,
            null
        );

        $helperProperty = $this->setAccessibleProperty($this->_bannerGeneratorReflector, '_helper');
        $helperProperty->setValue(
            $this->_bannerGeneratorMock,
            $helper
        );

        $generateEntity = $this->getMockObjectWithoutConstructor(
            EntityBanner::class,
            ['getUpgradeFields']
        );

        $generateEntity->expects($this->any())
            ->method('getUpgradeFields')
            ->willReturn(
                $this->_entityFields
            );

        $generateEntityProperty = $this->setAccessibleProperty($this->_bannerGeneratorReflector, '_generateEntity');
        $generateEntityProperty->setValue(
            $this->_bannerGeneratorMock,
            $generateEntity
        );

        $eventManager = $this->getMockObjectWithoutConstructor(
            ManagerInterface::class,
            ['dispatch']
        );
        $eventManagerProperty = $this->setAccessibleProperty($this->_bannerGeneratorReflector, '_eventManager');
        $eventManagerProperty->setValue(
            $this->_bannerGeneratorMock,
            $eventManager
        );

        $storeManagerMock = $this->getMockObjectWithoutConstructor(
            StoreManager::class,
            ['getStores']
        );

        $storeManagerProperty = $this->setAccessibleProperty($this->_bannerGeneratorReflector, '_storeManager');
        $storeManagerProperty->setValue(
            $this->_bannerGeneratorMock,
            $storeManagerMock
        );

        $storeModel = $this->getMockObjectWithoutConstructor(
            Store::class,
            ['getId']
        );
        $storeModelSecond = $this->getMockObjectWithoutConstructor(
            Store::class,
            ['getId']
        );

        $storeModel->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $storeModelSecond->expects($this->any())
            ->method('getId')
            ->willReturn(2);

        $storeManagerMock->expects($this->any())
            ->method('getStores')
            ->willReturn([
                $storeModel,
                $storeModelSecond
            ]);

    }

    /**
     * @param array $bannerIds
     * @dataProvider provideGetBannersData
     */
    public function testGetBannersData(array $bannerIds)
    {
        try {
            $methodUpgradeData = $this->_bannerGeneratorReflector->getMethod('_getUpgradeData');
            $methodUpgradeData->setAccessible(true);
            $upgradeData = $methodUpgradeData->invokeArgs($this->_bannerGeneratorMock, []);

            $method = $this->_bannerGeneratorReflector->getMethod('_getBannersData');
            $method->setAccessible(true);

            $dataProperty = $this->_bannerModelReflector->getProperty('_data');
            $dataProperty->setAccessible(true);

            if (!empty($bannerIds)) {
                foreach ($bannerIds as $keyBanner) {
                    $this->_bannerModelMock->expects($this->any())
                        ->method('load')
                        ->with($keyBanner)
                        ->willReturnSelf();
                    $dataProperty->setValue($this->_bannerModelMock, $this->_bannerData[$keyBanner]);
                    $this->assertEquals(
                        $this->_bannerDataOut[$keyBanner],
                        $method->invokeArgs($this->_bannerGeneratorMock, [[$keyBanner], $upgradeData])
                    );
                }
            }

        } catch (InvalidArgumentException $notExpected) {
            $this->fail();
        }
    }

    /**
     * @return array
     */
    public function provideGetBannersData()
    {
        return [
            [
                [1]
            ],
            [
                [2]
            ],
            [
                [3]
            ]
        ];
    }
}
