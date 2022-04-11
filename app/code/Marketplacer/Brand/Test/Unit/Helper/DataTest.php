<?php

namespace Marketplacer\Brand\Test\Unit\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Data;
use Marketplacer\Brand\Helper\Url as UrlHelper;
use Marketplacer\Brand\Model\Brand;
use Marketplacer\Brand\Model\BrandRepository;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


/**
 * Class DataTest
 * @package Marketplacer\Brand\Test\Unit\Helper
 */
class DataTest extends TestCase
{
    private $objectManager;
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Brand
     */
    private $brandObject;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    private $brandAttributeRetrieverMock;

    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->brandObject = $this->objectManager->getObject(Brand::class);
        $this->brandAttributeRetrieverMock = $this->getMockBuilder(
            BrandAttributeRetrieverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetBrandUrl()
    {
        $url = 'http://localhost.com/dev/';
        $this->brandObject = $this->brandObject->setData([
            BrandInterface::STORE_ID => 1,
            'brand_id' => 10,
        ]);
        $brandRepositoryMock = $this->getBrandRepositoryMock(
            $this->brandObject[BrandInterface::STORE_ID],
            $this->brandObject
        );
        $brandRepositoryMock->method('getById')->willReturn($this->brandObject);
        $urlHelper = $this->getUrlHelper($url);

        $brandHelperData = $this->objectManager->getObject(Data::class, [
            'brandAttributeRetriever' => $this->brandAttributeRetrieverMock,
            'brandRepository' => $brandRepositoryMock,
            'urlHelper' => $urlHelper,
        ]);
        $brandUrl = $brandHelperData->getBrandUrl($this->brandObject);
        $this->assertEquals($url, $brandUrl);
    }

    public function testGetBrandByProduct()
    {
        $this->brandObject = $this->brandObject->setData([
            BrandInterface::STORE_ID => 1,
            'brand_id' => 10,
        ]);
        $brandRepoMock = $this->getMockBuilder(BrandRepository::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMock();
        $brandRepoMock->expects($this->once())
            ->method('getById')
            ->willReturn($this->brandObject);

        $this->brandAttributeRetrieverMock = $this->getMockBuilder(BrandAttributeRetrieverInterface::class)
            ->setMethods(['getAttributeCode'])
            ->getMockForAbstractClass();
        $this->brandAttributeRetrieverMock->expects($this->once())->method('getAttributeCode')
            ->willReturn('brand_id');
        $urlHelper = $this->getMockBuilder(UrlHelper::class)
            ->disableOriginalConstructor()->getMock();

        $brandHelperData = $this->objectManager->getObject(Data::class,
            [
                'brandAttributeRetriever' => $this->brandAttributeRetrieverMock,
                'brandRepository' => $brandRepoMock,
                'urlHelper' => $urlHelper,
            ]
        );
        $this->product = $this->createMock(Product::class);
        $this->product->method('getData')->willReturn(10);
        $this->product->method('getStoreId')->willReturn(1);
        $this->product->setData('brand_id', 10);

        $brandInfo = $brandHelperData->getBrandByProduct($this->product);
        $this->assertEquals($this->brandObject->getData(), $brandInfo->getData());
    }

    /**
     * @return void
     */
    public function testGetBrandEmptyUrl()
    {
        $url = '';
        $this->brandObject = $this->brandObject->setData([
        ]);
        $brandRepositoryMock = $this->getBrandRepositoryMock(
            $this->brandObject[BrandInterface::STORE_ID],
            $this->brandObject
        );
        $urlHelper = $this->getUrlHelper($url);

        $brandHelperData = $this->objectManager->getObject(Data::class, [
            'brandAttributeRetriever' => $this->brandAttributeRetrieverMock,
            'brandRepository' => $brandRepositoryMock,
            'urlHelper' => $urlHelper,
        ]);
        $brandUrl = $brandHelperData->getBrandUrl($this->brandObject);
        $this->assertEquals('', $brandUrl);
    }

    public function testGetBrandWithEmptyBrandId()
    {
        $brandRepoMock = $this->getMockBuilder(BrandRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->brandAttributeRetrieverMock = $this->getMockBuilder(BrandAttributeRetrieverInterface::class)
            ->setMethods(['getAttributeCode'])
            ->getMockForAbstractClass();
        $this->brandAttributeRetrieverMock->expects($this->once())->method('getAttributeCode')
            ->willReturn('brand_id');
        $urlHelper = $this->getMockBuilder(UrlHelper::class)
            ->disableOriginalConstructor()->getMock();

        $brandHelperData = $this->objectManager->getObject(Data::class, [
            'brandAttributeRetriever' => $this->brandAttributeRetrieverMock,
            'brandRepository' => $brandRepoMock,
            'urlHelper' => $urlHelper,
        ]);
        $this->product = $this->createMock(Product::class);
        $this->product->method('getData')->willReturn(null);
        $brandInfo = $brandHelperData->getBrandByProduct($this->product);
        $this->assertEquals(null, $brandInfo);
    }

    /**
     * @param int $storeId
     * @param $brand
     * @return BrandRepository|MockObject
     */
    private function getBrandRepositoryMock($storeId, $brand)
    {
        $productStoreId = 1;
        $brandRepoMock = $this->getMockBuilder(BrandRepository::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMock();
        $brandRepoMock->expects($this->any())
            ->method('getById')->with($storeId, $productStoreId)
            ->willReturn($brand);

        return $brandRepoMock;
    }

    /**
     * @return UrlHelper|MockObject
     */
    private function getUrlHelper($url)
    {
        $urlHelper = $this->getMockBuilder(UrlHelper::class)
            ->disableOriginalConstructor()->setMethods(['getBrandUrlById'])->getMock();
        $urlHelper->expects($this->once())->method($this->anything())->willReturn($url);
        return $urlHelper;
    }
}
