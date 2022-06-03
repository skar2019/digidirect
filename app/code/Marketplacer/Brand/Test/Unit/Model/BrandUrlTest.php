<?php

namespace Marketplacer\Brand\Test\Unit\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Url as UrlHelper;
use Marketplacer\Brand\Model\BrandUrl;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BrandUrlTest extends TestCase
{
    /**
     * @var UrlHelper|MockObject
     */
    protected $urlHelperMock;

    /**
     * @var BrandUrl
     */
    private $brandUrl;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->urlHelperMock = $this->createMock(\Marketplacer\Brand\Helper\Url::class);

        $this->brandUrl = $this->objectManager->getObject(
            \Marketplacer\Brand\Model\BrandUrl::class,
            [
                'urlHelper'                  => $this->urlHelperMock,
            ]
        );
    }

    public function testGetBrandUrlExisting()
    {
        $brandId = 5;
        $storeId = 1;
        $url = 'https://test-brand.url';

        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brand->setData(
            [
                BrandInterface::BRAND_ID => $brandId,
                BrandInterface::STORE_ID => $storeId,
            ]
        );
        $this->urlHelperMock->method('getBrandUrlById')->willReturn($url);

        $this->assertEquals($url, $this->brandUrl->getBrandUrl($brand));
    }

    public function testGetBrandUrlMissing()
    {
        $brandId = 5;
        $storeId = 1;
        $url = 'https://test-brand.url';

        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brand->setData(
            [
                BrandInterface::BRAND_ID => $brandId,
                BrandInterface::STORE_ID => $storeId,
            ]
        );
        $this->urlHelperMock->method('getBrandUrlById')->willThrowException(new NoSuchEntityException());

        $this->assertNull($this->brandUrl->getBrandUrl($brand));
    }
}
