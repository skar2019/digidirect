<?php

namespace Marketplacer\Brand\Test\Unit\Model\UrlProcessor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlFactory as MagentoUrlFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\StorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Helper\Config as ConfigHelper;
use Marketplacer\Brand\Model\Brand;
use Marketplacer\Brand\Model\UrlProcessor\BrandProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BrandProcessorTest extends TestCase
{
    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var UrlRewriteFactory|MockObject
     */
    private $urlRewriteFactoryMock;

    /**
     * @var StorageInterface|MockObject
     */
    private $storageMock;

    /**
     * @var MagentoUrlFactory|MockObject
     */
    private $urlFactoryMock;

    /**
     * @var ConfigHelper|MockObject
     */
    private $configHelperMock;

    /**
     * @var BrandRepositoryInterface|MockObject
     */
    private $brandRepositoryMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var BrandProcessor
     */
    private $brandProcessor;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $this->storeManagerMock->method('getStores')->willReturn([
            1 => $this->objectManager->getObject(\Magento\Store\Model\Store::class)->setData('store_id', 1),
            2 => $this->objectManager->getObject(\Magento\Store\Model\Store::class)->setData('store_id', 2),
        ]);

        $this->storageMock = $this->createMock(\Magento\UrlRewrite\Model\StorageInterface::class);
        $this->brandRepositoryMock = $this->createMock(\Marketplacer\Brand\Model\BrandRepository::class);;

        $this->configHelperMock = $this->createMock(\Marketplacer\Brand\Helper\Config::class);;
        $this->configHelperMock->method('getBaseUrlKey')->withConsecutive([1], [2])->willReturnOnConsecutiveCalls('brands_1', 'brands_2');
        $this->configHelperMock->method('getUrlSuffix')->withConsecutive([1], [2])->willReturnOnConsecutiveCalls('-1.html', '-2.html');

        $this->urlRewriteFactoryMock = $this->createMock(\Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory::class);;
        $this->urlRewriteFactoryMock->method('create')->willReturnCallback(function ($data) {
            return $this->objectManager->getObject(UrlRewrite::class, $data);
        });

        $this->brandProcessor = $this->objectManager->getObject(
            BrandProcessor::class,
            [
                'storeManager'      => $this->storeManagerMock,
                'urlRewriteFactory' => $this->urlRewriteFactoryMock,
                'storage'           => $this->storageMock,
                'urlFactory'        => $this->urlFactoryMock,
                'configHelper'      => $this->configHelperMock,
                'brandRepository'  => $this->brandRepositoryMock,
            ]
        );
    }

    public function testProcessBrandUrlRewrites()
    {
        $brandId = '5';
        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(Brand::class);
        $brand->setData([
            BrandInterface::BRAND_ID => $brandId,
            BrandInterface::URL_KEY   => 'test-brand'
        ]);

        $this->brandRepositoryMock
            ->method('getAllStoreRecordsById')
            ->willReturn([$brand]);

        /** @var UrlRewrite $urlRewrite1 */
        $urlRewrite1 = $this->objectManager->getObject(
            UrlRewrite::class,
            [
                'data' => [
                    UrlRewrite::ENTITY_TYPE => BrandProcessor::URL_ENTITY_TYPE,
                    UrlRewrite::ENTITY_ID => $brandId,
                    UrlRewrite::REQUEST_PATH => 'brands_1/test-brand-1.html',
                    UrlRewrite::TARGET_PATH => sprintf(BrandProcessor::BRAND_VIEW_TARGET_PATH_PATTERN, $brandId),
                    UrlRewrite::STORE_ID => 1
                ]
            ]
        );

        /** @var UrlRewrite $urlRewrite2 */
        $urlRewrite2 = $this->objectManager->getObject(
            UrlRewrite::class,
            [
                'data' => [
                    UrlRewrite::ENTITY_TYPE => BrandProcessor::URL_ENTITY_TYPE,
                    UrlRewrite::ENTITY_ID => $brandId,
                    UrlRewrite::REQUEST_PATH => 'brands_2/test-brand-2.html',
                    UrlRewrite::TARGET_PATH => sprintf(BrandProcessor::BRAND_VIEW_TARGET_PATH_PATTERN, $brandId),
                    UrlRewrite::STORE_ID => 2
                ]
            ]
        );

        $this->storageMock->method('replace')->with([$urlRewrite1, $urlRewrite2]);

        $this->brandProcessor->processBrandUrlRewrites($brand);
    }

    public function testProcessBrandUrlRewritesWithMissingBrand()
    {
        $brandId = '5';
        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(Brand::class);
        $brand->setData([
            BrandInterface::BRAND_ID => $brandId,
            BrandInterface::URL_KEY   => 'test-brand'
        ]);

        $this->brandRepositoryMock
            ->method('getAllStoreRecordsById')
            ->willThrowException(new NoSuchEntityException());

        $this->expectException(NoSuchEntityException::class);

        $this->brandProcessor->processBrandUrlRewrites($brand);
    }

    public function testDeleteUrlRewrites()
    {
        $brandId = '5';
        $storeId = '1';

        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(Brand::class);
        $brand->setData([
            BrandInterface::BRAND_ID => $brandId,
            BrandInterface::URL_KEY   => 'test-brand'
        ]);

        $deletionFilterData = [
            UrlRewrite::ENTITY_ID   => $brandId,
            UrlRewrite::ENTITY_TYPE => BrandProcessor::URL_ENTITY_TYPE,
            UrlRewrite::STORE_ID => $storeId,
        ];
        $this->storageMock->method('deleteByData')->with($deletionFilterData);

        $this->brandProcessor->deleteUrlRewrites($brand, $storeId);
    }


    public function testProcessBrandListingUrlRewrites()
    {
        /** @var UrlRewrite $urlRewrite1 */
        $urlRewrite1 = $this->objectManager->getObject(
            UrlRewrite::class,
            [
                'data' => [
                    UrlRewrite::ENTITY_TYPE => BrandProcessor::URL_ENTITY_TYPE,
                    UrlRewrite::ENTITY_ID => 0,
                    UrlRewrite::REQUEST_PATH => 'brands_1-1.html',
                    UrlRewrite::TARGET_PATH => BrandProcessor::BRAND_LIST_TARGET_PATH_PATTERN,
                    UrlRewrite::STORE_ID => 1
                ]
            ]
        );

        /** @var UrlRewrite $urlRewrite2 */
        $urlRewrite2 = $this->objectManager->getObject(
            UrlRewrite::class,
            [
                'data' => [
                    UrlRewrite::ENTITY_TYPE => BrandProcessor::URL_ENTITY_TYPE,
                    UrlRewrite::ENTITY_ID => 0,
                    UrlRewrite::REQUEST_PATH => 'brands_2-2.html',
                    UrlRewrite::TARGET_PATH => BrandProcessor::BRAND_LIST_TARGET_PATH_PATTERN,
                    UrlRewrite::STORE_ID => 2
                ]
            ]
        );

        $this->storageMock->method('replace')->with([$urlRewrite1, $urlRewrite2]);

        $this->brandProcessor->processBrandListingUrlRewrites();
    }
}
