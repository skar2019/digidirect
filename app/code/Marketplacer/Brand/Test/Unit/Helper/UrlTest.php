<?php


namespace Marketplacer\Brand\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlFactory as MagentoUrlFactory;
use Magento\Store\Model\Store;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Url;
use Marketplacer\Brand\Model\Brand;
use Marketplacer\Brand\Model\BrandRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UrlTest
 * @package Marketplacer\Brand\Test\Unit\Helper
 */
class UrlTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Brand
     */
    private $brandObject;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerMock;

    /**
     * @var MagentoUrlFactory|MockObject
     */
    private $urlFactoryMock;

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var Url
     */
    private $brandUrlHelper;

    /**
     * @var BrandRepository|MockObject
     */
    private $brandRepositoryMock;

    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->storeManagerMock->method('getStore')->willReturn(
            $this->objectManager->getObject(Store::class)->setData('store_id', 1),
        );
        $this->brandObject = $this->objectManager->getObject(Brand::class);
        $this->brandObject = $this->brandObject->setData([
            BrandInterface::STORE_ID => 1,
            BrandInterface::BRAND_ID => 10,
        ]);
        $this->brandRepositoryMock = $this->getBrandRepositoryMock();
        $this->urlFinder = $this->getMockForAbstractClass(UrlFinderInterface::class);

        $this->urlFactoryMock = $this->getMockBuilder(MagentoUrlFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
    }

    public function testGetBrandUrlById()
    {
        $url = 'http://localhost.com/dev/';
        $brandId = 10;
        $storeId = 1;
        $routeParams['_direct'] = 'marketplacer/brand/view/brand_id/10';
        $routeParams['_query'] = [];
        $routePath = '';
        $urlMock = $this->getMockBuilder(\Magento\Framework\Url::class)
            ->disableOriginalConstructor()
            ->setMethods(['setScope', 'getUrl'])
            ->getMock();
        $urlMock->expects($this->any())
            ->method('setScope')
            ->will($this->returnValue($urlMock));
        $urlMock->expects($this->once())
            ->method('getUrl')
            ->with($routePath, $routeParams)
            ->willReturn($url);

        $this->urlFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($urlMock));
        $this->brandUrlHelper = $this->objectManager->getObject(
            Url::class,
            [
                'brandRepositoryInterface' =>  $this->brandRepositoryMock,
                'storeManager'             => $this->storeManagerMock,
                'urlFinder'                => $this->urlFinder,
                'urlFactory'               => $this->urlFactoryMock,
            ]
        );
        $brandUrl = $this->brandUrlHelper->getBrandUrlById($brandId, $storeId, []);
        $this->assertEquals($url, $brandUrl);
    }

    public function testGetBrandUrlByIdWithoutBrandId()
    {
        $brandId = 10;
        $storeId = 1;
        $this->brandObject = $this->brandObject->setData([
            BrandInterface::STORE_ID => 1,
        ]);
        $this->brandUrlHelper = $this->objectManager->getObject(
            Url::class,
            [
                'brandRepositoryInterface' =>  $this->brandRepositoryMock,
                'storeManager'             => $this->storeManagerMock,
                'urlFinder'                => $this->urlFinder,
                'urlFactory'               => $this->urlFactoryMock,
            ]
        );
        $brandUrl = $this->brandUrlHelper->getBrandUrlById($brandId, $storeId);
        $this->assertEquals(null, $brandUrl);
    }

    public function testGetBrandListingUrl()
    {
        $url = 'http://localhost.com/dev/';
        $routeParams['_direct'] = 'marketplacer/brand/index';
        $routeParams['_query'] = [];
        $routePath = '';
        $urlMock = $this->getMockBuilder(\Magento\Framework\Url::class)
            ->disableOriginalConstructor()
            ->setMethods(['setScope', 'getUrl'])
            ->getMock();
        $urlMock->expects($this->once())
            ->method('setScope')
            ->will($this->returnValue($urlMock));

        $urlMock->expects($this->once())
            ->method('getUrl')
            ->with($routePath, $routeParams)
            ->willReturn($url);
        $this->urlFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($urlMock));
        $this->brandUrlHelper = $this->objectManager->getObject(
            Url::class,
            [
                'brandRepositoryInterface' =>  $this->brandRepositoryMock,
                'storeManager'             => $this->storeManagerMock,
                'urlFinder'                => $this->urlFinder,
                'urlFactory'               => $this->urlFactoryMock,
            ]
        );
        $brandUrl = $this->brandUrlHelper->getBrandListingUrl();
        $this->assertEquals($url, $brandUrl);
    }

    public function testGetBrandUrl()
    {
        $url = 'http://localhost.com/dev/';
        $routeParams['_direct'] = 'marketplacer/brand/view/brand_id/10';
        $routeParams['_query'] = [];
        $routePath = '';
        $urlMock = $this->getMockBuilder(\Magento\Framework\Url::class)
            ->disableOriginalConstructor()
            ->setMethods(['setScope', 'getUrl'])
            ->getMock();
        $urlMock->expects($this->any())
            ->method('setScope')
            ->will($this->returnValue($urlMock));

        $urlMock->expects($this->once())
            ->method('getUrl')
            ->with($routePath, $routeParams)
            ->willReturn($url);
        $this->urlFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($urlMock));
        $this->brandUrlHelper = $this->objectManager->getObject(
            Url::class,
            [
                'brandRepositoryInterface' =>  $this->brandRepositoryMock,
                'storeManager'             => $this->storeManagerMock,
                'urlFinder'                => $this->urlFinder,
                'urlFactory'               => $this->urlFactoryMock,
            ]
        );
        $brandUrl = $this->brandUrlHelper->getBrandUrl($this->brandObject, []);
        $this->assertEquals($url, $brandUrl);
    }

    /**
     * @return BrandRepository|MockObject
     */
    private function getBrandRepositoryMock()
    {
        $brandRepoMock = $this->getMockBuilder(BrandRepository::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMock();
        $brandRepoMock->expects($this->any())
            ->method('getById')
            ->willReturn($this->brandObject);
        return $brandRepoMock;
    }
}
