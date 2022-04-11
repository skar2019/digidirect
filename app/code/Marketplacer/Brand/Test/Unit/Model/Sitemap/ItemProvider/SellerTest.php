<?php

namespace Marketplacer\Brand\Test\Unit\Model\Sitemap\ItemProvider;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sitemap\Model\ItemProvider\CategoryConfigReader;
use Magento\Sitemap\Model\SitemapItem;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Config as ConfigHelper;
use Marketplacer\Brand\Helper\Url as UrlHelper;
use Marketplacer\Brand\Model\BrandRepository;
use Marketplacer\Brand\Model\Sitemap\ItemProvider\Brand;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class BrandTest
 * @package Marketplacer\Brand\Test\Unit\Model\Sitemap\ItemProvider
 */
class BrandTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function testBrandConfigDisabled() {
        $storeId = 1;
        $url = '';
        $this->objectManager = new ObjectManager($this);
        $this->brandObject = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brands[] = $this->brandObject->setData([]);

        $brandCollectionMock = $this->getBrandCollectionMock($storeId, $brands);
        $urlHelper = $this->getUrlHelper($url);
        $configHelperMock = $this->getDisabledConfigHelper($storeId);
        $itemFactoryMock = $this->getItemFactoryMock();
        $configReaderMock = $this->getConfigReaderMock();

        $brand = $this->objectManager->getObject(Brand::class,
            [
                'brandRepository'     => $brandCollectionMock,
                'urlHelper'            => $urlHelper,
                'configHelper'         => $configHelperMock,
                'sitemapItemFactory'   => $itemFactoryMock,
                'categoryConfigReader' => $configReaderMock,
            ]
        );
        $items = $brand->getItems($storeId);
        $this->assertEquals([], $items);
    }

    public function testGetItemsEmpty()
    {
        $storeId = 1;
        $url = null;
        $this->objectManager = new ObjectManager($this);
        $this->brandObject = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brands[] = $this->brandObject->setData([]);

        $brandCollectionMock = $this->getBrandCollectionMock($storeId, []);
        $urlHelper = $this->getUrlHelper($url);
        $configHelperMock = $this->getConfigHelper($storeId);
        $itemFactoryMock = $this->getItemFactoryMock();
        $configReaderMock = $this->getConfigReaderMock();
        $brand = $this->objectManager->getObject(Brand::class,
            [
                'brandRepository'     => $brandCollectionMock,
                'urlHelper'            => $urlHelper,
                'configHelper'         => $configHelperMock,
                'sitemapItemFactory'   => $itemFactoryMock,
                'categoryConfigReader' => $configReaderMock,
            ]
        );
        $items = $brand->getItems($storeId);
        $this->assertNotEquals($brands,$items);
    }

    public function testGetItems() {
        $storeId = 1;
        $url = 'http://localhost.com/dev/';
        $this->objectManager = new ObjectManager($this);
        $this->brandObject = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brands[] = $this->brandObject->setData([
            BrandInterface::STORE_ID => 1,
            MarketplacerBrandInterface::BRAND_ID => 10,
            'updatedAt' => '2021-11-23 12:02:25',
            'url'=> $url,
        ]);

        $brandCollectionMock = $this->getBrandCollectionMock($storeId, $brands);
        $urlHelper = $this->getUrlHelper($url);
        $configHelperMock = $this->getConfigHelper($storeId);
        $itemFactoryMock = $this->getItemFactoryMock();
        $configReaderMock = $this->getConfigReaderMock();

        $brand = $this->objectManager->getObject(Brand::class,
            [
                'brandRepository'     => $brandCollectionMock,
                'urlHelper'            => $urlHelper,
                'configHelper'         => $configHelperMock,
                'sitemapItemFactory'   => $itemFactoryMock,
                'categoryConfigReader' => $configReaderMock,
            ]
        );
        $items = $brand->getItems($storeId);
        foreach ($brands as $index => $brand) {
            self::assertSame($brand->getUpdatedAt(), $items[$index]->getUpdatedAt());
            self::assertSame('daily', $items[$index]->getChangeFrequency());
            self::assertSame('1.0', $items[$index]->getPriority());
            self::assertSame($brand->getImages(), $items[$index]->getImages());
            self::assertSame($brand->getUrl(), $items[$index]->getUrl());
        }
    }

    /**
     * @return MockObject
     */
    private function getConfigReaderMock()
    {
        $configReaderMock = $this->getMockBuilder(CategoryConfigReader::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPriority','getChangeFrequency'])
            ->getMockForAbstractClass();
        $configReaderMock->expects($this->any())
            ->method('getPriority')
            ->willReturn('1.0');
        $configReaderMock->expects($this->any())
            ->method('getChangeFrequency')
            ->willReturn('daily');

        return $configReaderMock;
    }

    /**
     * @param int $storeId
     * @param array $brands
     * @return BrandRepository|MockObject
     */
    private function getBrandCollectionMock($storeId, $brands)
    {
        $brandRepoMock = $this->getMockBuilder(BrandRepository::class)
            ->setMethods(['getAllDisplayedBrands'])
            ->disableOriginalConstructor()
            ->getMock();
        $brandRepoMock->expects($this->any())
            ->method('getAllDisplayedBrands')->with($storeId)
            ->willReturn($brands);

        return $brandRepoMock;
    }

    /**
     * @param int $storeId
     * @return ConfigHelper|MockObject
     */
    private function getConfigHelper($storeId)
    {
        $configHelperMock = $this->getMockBuilder(ConfigHelper::class)
            ->disableOriginalConstructor()->setMethods(['isEnabledOnStorefront'])
            ->getMockForAbstractClass();
        $configHelperMock->expects($this->once())
            ->method('isEnabledOnStorefront')
            ->with($storeId)
            ->willReturn(true);
        return $configHelperMock;
    }

    /**
     * @return UrlHelper|MockObject
     */
    private function getUrlHelper($url)
    {
        $urlHelper = $this->getMockBuilder(UrlHelper::class)
            ->disableOriginalConstructor()->setMethods(['getBrandListingUrl','getBrandUrl'])->getMock();
        $urlHelper->expects($this->any())->method($this->anything())->willReturn($url);
        return $urlHelper;
    }

    /**
     * @return MockObject
     */
    private function getItemFactoryMock()
    {
        $itemFactoryMock = $this->getMockBuilder(SitemapItemInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $itemFactoryMock->expects($this->any())
            ->method('create')
            ->willReturnCallback(function ($data) {
                $helper = new ObjectManager($this);
                return $helper->getObject(SitemapItem::class, $data);
            });

        return $itemFactoryMock;
    }

    /**
     * @param int $storeId
     * @return ConfigHelper|MockObject
     */
    private function getDisabledConfigHelper($storeId)
    {
        $configHelperMock = $this->getMockBuilder(ConfigHelper::class)
            ->disableOriginalConstructor()->setMethods(['isEnabledOnStorefront'])
            ->getMockForAbstractClass();
        $configHelperMock->expects($this->once())
            ->method('isEnabledOnStorefront')
            ->with($storeId)
            ->willReturn(false);
        return $configHelperMock;
    }
}
