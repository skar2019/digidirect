<?php

namespace Marketplacer\Marketplacer\Test\Unit\Plugin;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Escaper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use Marketplacer\BrandApi\Api\Data\OrderItemInterface as OrderItemInterfaceBrand;
use Marketplacer\Marketplacer\Helper\Data as MarketplacerDataHelper;
use Marketplacer\Marketplacer\Plugin\AbstractItemOptionsPlugin;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface;
use Marketplacer\SellerApi\Api\Data\OrderItemInterface as OrderItemInterfaceSeller;
use Marketplacer\SellerApi\Api\MarketplacerSellerUrlInterface;
use Marketplacer\SellerApi\Api\SellerAttributeRetrieverInterface;
use Marketplacer\SellerApi\Api\SellerRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractItemOptionsPluginTest extends TestCase
{
    use \Marketplacer\Base\Test\Unit\Traits\ReflectionTrait;

    /**
     * @var MarketplacerDataHelper|MockObject
     */
    private $marketplacerDataHelperMock;

    /**
     * @var BrandAttributeRetrieverInterface|MockObject
     */
    private $brandAttributeRetrieverMock;

    /**
     * @var string
     */
    private $brandAttributeCode = 'marketplacer_brand';

    /**
     * @var SellerAttributeRetrieverInterface|MockObject
     */
    private $sellerAttributeRetrieverMock;

    /**
     * @var string
     */
    private $sellerAttributeCode = 'marketplacer_seller';

    /**
     * @var SellerRepositoryInterface|MockObject
     */
    private $sellerRepositoryMock;

    /**
     * @var MarketplacerSellerUrlInterface|MockObject
     */
    private $sellerUrlMock;

    /**
     * @var ProductResource|MockObject
     */
    private $productResourceMock;

    /**
     * @var Escaper|MockObject
     */
    private $escaperMock;

    /**
     * @var AbstractItemOptionsPlugin
     */
    private $abstractItemOptionsPlugin;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->marketplacerDataHelperMock = $this->createMock(\Marketplacer\Marketplacer\Helper\Data::class);

        $this->brandAttributeRetrieverMock = $this->createMock(\Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface::class);
        $brandAttribute = $this->createMock(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $brandAttribute->method('getAttributeCode')->willReturn($this->brandAttributeCode);
        $this->brandAttributeSourceMock = $this->createMock(\Magento\Eav\Model\Entity\Attribute\Source\AbstractSource::class);
        $brandAttribute->method('getSource')->willReturn($this->brandAttributeSourceMock);
        $this->brandAttributeRetrieverMock->method('getAttribute')->willReturn($brandAttribute);

        $this->sellerAttributeRetrieverMock = $this->createMock(\Marketplacer\SellerApi\Api\SellerAttributeRetrieverInterface::class);
        $sellerAttribute = $this->createMock(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $sellerAttribute->method('getAttributeCode')->willReturn($this->sellerAttributeCode);
        $this->sellerAttributeSourceMock = $this->createMock(\Magento\Eav\Model\Entity\Attribute\Source\AbstractSource::class);
        $sellerAttribute->method('getSource')->willReturn($this->sellerAttributeSourceMock);
        $this->sellerAttributeRetrieverMock->method('getAttribute')->willReturn($sellerAttribute);

        $this->sellerRepositoryMock = $this->createMock(\Marketplacer\SellerApi\Api\SellerRepositoryInterface::class);
        $this->productResourceMock = $this->createMock(\Magento\Catalog\Model\ResourceModel\Product::class);
        $this->sellerUrlMock = $this->createMock(\Marketplacer\SellerApi\Api\MarketplacerSellerUrlInterface::class);
        $this->escaperMock = $this->createMock(\Magento\Framework\Escaper::class);

        $this->abstractItemOptionsPlugin = $this->objectManager->getObject(
            \Marketplacer\Marketplacer\Test\Unit\Plugin\TestedItemOptionsPlugin::class,
            [
                'marketplacerDataHelper'   => $this->marketplacerDataHelperMock,
                'brandAttributeRetriever'  => $this->brandAttributeRetrieverMock,
                'sellerAttributeRetriever' => $this->sellerAttributeRetrieverMock,
                'sellerRepository'         => $this->sellerRepositoryMock,
                'productResource'          => $this->productResourceMock,
                'sellerUrl'                => $this->sellerUrlMock,
                'escaper'                  => $this->escaperMock,
            ]
        );
    }

    public function testAddOptionsToQuoteItemOptionsAllFilledAndEnabled()
    {
        $sellerId = 5;
        $brandId = 105;
        $storeId = 1;

        $quoteItem = $this->objectManager->getObject(\Magento\Framework\DataObject::class);
        $quoteItem->setProduct($this->getProductMockWithSellerIdAndBrandId($sellerId, $brandId));
        $quoteItem->setStoreId($storeId);

        $this->marketplacerDataHelperMock->method('displaySellerBusinessNumber')->willReturn(true);
        $this->marketplacerDataHelperMock->method('displayBrandName')->willReturn(true);
        $this->marketplacerDataHelperMock->method('getTitleForSellerBusinessNumber')->willReturn('ABN Title');

        $this->sellerRepositoryMock->method('getById')->with($sellerId, $storeId)->willReturn($this->getSellerWithId5());

        $this->sellerAttributeSourceMock->method('getOptionText')->with($sellerId)->willReturn('Seller Name 1');
        $this->brandAttributeSourceMock->method('getOptionText')->with($brandId)->willReturn('Brand Name 1');

        $initialOptions = [
            ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter']
        ];
        $this->assertEquals(
            [
                ['label' => 'Seller', 'value' => 'Seller Name 1', 'print_value' => 'Seller Name 1'],
                ['label' => 'ABN Title', 'value' => 'ABN1', 'print_value' => 'ABN1'],
                ['label' => 'Brand', 'value' => 'Brand Name 1', 'print_value' => 'Brand Name 1'],
                ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter'],
            ],
            $this->invokeMethod(
                $this->abstractItemOptionsPlugin,
                'addOptionsToQuoteItemOptions',
                [$quoteItem, $initialOptions, false]
            )
        );
    }

    public function testAddOptionsToQuoteItemOptionsAllFilledAndBrandConfigDisabled()
    {
        $sellerId = 5;
        $brandId = 105;
        $storeId = 1;

        $quoteItem = $this->objectManager->getObject(\Magento\Framework\DataObject::class);
        $quoteItem->setProduct($this->getProductMockWithSellerIdAndBrandId($sellerId, $brandId));
        $quoteItem->setStoreId($storeId);

        $this->marketplacerDataHelperMock->method('displaySellerBusinessNumber')->willReturn(true);
        $this->marketplacerDataHelperMock->method('displayBrandName')->willReturn(false);
        $this->marketplacerDataHelperMock->method('getTitleForSellerBusinessNumber')->willReturn('ABN Title');

        $this->sellerRepositoryMock->method('getById')->with($sellerId, $storeId)->willReturn($this->getSellerWithId5());

        $this->sellerAttributeSourceMock->method('getOptionText')->with($sellerId)->willReturn('Seller Name 1');
        $this->brandAttributeSourceMock->expects($this->never())->method('getOptionText');

        $initialOptions = [
            ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter']
        ];
        $this->assertEquals(
            [
                ['label' => 'Seller', 'value' => 'Seller Name 1', 'print_value' => 'Seller Name 1'],
                ['label' => 'ABN Title', 'value' => 'ABN1', 'print_value' => 'ABN1'],
                ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter'],
            ],
            $this->invokeMethod(
                $this->abstractItemOptionsPlugin,
                'addOptionsToQuoteItemOptions',
                [$quoteItem, $initialOptions, false]
            )
        );
    }

    public function testAddOptionsToQuoteItemOptionsAllFilledAndAbnConfigDisabled()
    {
        $sellerId = 5;
        $brandId = 105;
        $storeId = 1;

        $quoteItem = $this->objectManager->getObject(\Magento\Framework\DataObject::class);
        $quoteItem->setProduct($this->getProductMockWithSellerIdAndBrandId($sellerId, $brandId));
        $quoteItem->setStoreId($storeId);

        $this->marketplacerDataHelperMock->method('displaySellerBusinessNumber')->willReturn(false);
        $this->marketplacerDataHelperMock->method('displayBrandName')->willReturn(true);
        $this->marketplacerDataHelperMock->method('getTitleForSellerBusinessNumber')->willReturn('ABN Title');

        $this->sellerRepositoryMock->method('getById')->with($sellerId, $storeId)->willReturn($this->getSellerWithId5());

        $this->sellerAttributeSourceMock->method('getOptionText')->with($sellerId)->willReturn('Seller Name 1');
        $this->brandAttributeSourceMock->method('getOptionText')->with($brandId)->willReturn('Brand Name 1');

        $initialOptions = [
            ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter']
        ];
        $this->assertEquals(
            [
                ['label' => 'Seller', 'value' => 'Seller Name 1', 'print_value' => 'Seller Name 1'],
                ['label' => 'Brand', 'value' => 'Brand Name 1', 'print_value' => 'Brand Name 1'],
                ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter'],
            ],
            $this->invokeMethod(
                $this->abstractItemOptionsPlugin,
                'addOptionsToQuoteItemOptions',
                [$quoteItem, $initialOptions, false]
            )
        );
    }

    public function testAddOptionsToQuoteItemOptionsAllFilledAndEnabledWithUrl()
    {
        $sellerId = 5;
        $brandId = 105;
        $storeId = 1;

        $quoteItem = $this->objectManager->getObject(\Magento\Framework\DataObject::class);
        $quoteItem->setProduct($this->getProductMockWithSellerIdAndBrandId($sellerId, $brandId));
        $quoteItem->setStoreId($storeId);

        $this->marketplacerDataHelperMock->method('displaySellerBusinessNumber')->willReturn(true);
        $this->marketplacerDataHelperMock->method('displayBrandName')->willReturn(true);
        $this->marketplacerDataHelperMock->method('getTitleForSellerBusinessNumber')->willReturn('ABN Title');

        $seller5 = $this->getSellerWithId5();
        $this->sellerRepositoryMock->method('getById')->with($sellerId, $storeId)->willReturn($this->getSellerWithId5());

        $this->sellerAttributeSourceMock->method('getOptionText')->with($sellerId)->willReturn('Seller Name 1');
        $this->brandAttributeSourceMock->method('getOptionText')->with($brandId)->willReturn('Brand Name 1');

        $this->sellerUrlMock->method('getSellerUrl')->with($seller5)->willReturn('https://test-seller.url');

        $initialOptions = [
            ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter']
        ];
        $this->assertEquals(
            [
                ['label' => 'Seller', 'value' => '<a href="https://test-seller.url" target="_blank">Seller Name 1</a>', 'print_value' => 'Seller Name 1'],
                ['label' => 'ABN Title', 'value' => 'ABN1', 'print_value' => 'ABN1'],
                ['label' => 'Brand', 'value' => 'Brand Name 1', 'print_value' => 'Brand Name 1'],
                ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter'],
            ],
            $this->invokeMethod(
                $this->abstractItemOptionsPlugin,
                'addOptionsToQuoteItemOptions',
                [$quoteItem, $initialOptions, true]
            )
        );
    }

    public function testAddOptionsToOrderItemOptionsAllFilledAndEnabled()
    {
        $sellerId = 5;
        $brandId = 105;
        $storeId = 1;

        $orderItem = $this->objectManager->getObject(\Magento\Sales\Model\Order\Item::class);
        $orderItem->setData(
            [
                OrderItemInterfaceSeller::SELLER_NAME            => 'Seller Name 1',
                OrderItemInterfaceSeller::SELLER_ID              => $sellerId,
                OrderItemInterfaceSeller::SELLER_BUSINESS_NUMBER => 'ABN1',
                OrderItemInterfaceBrand::BRAND_NAME              => 'Brand Name 1'
            ]
        );

        $this->marketplacerDataHelperMock->method('displaySellerBusinessNumber')->willReturn(true);
        $this->marketplacerDataHelperMock->method('displayBrandName')->willReturn(true);
        $this->marketplacerDataHelperMock->method('getTitleForSellerBusinessNumber')->willReturn('ABN Title');

        $this->sellerRepositoryMock->method('getById')->with($sellerId, $storeId)->willReturn($this->getSellerWithId5());

        $initialOptions = [
            ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter']
        ];
        $this->assertEquals(
            [
                ['label' => 'Seller', 'value' => 'Seller Name 1', 'print_value' => 'Seller Name 1'],
                ['label' => 'ABN Title', 'value' => 'ABN1', 'print_value' => 'ABN1'],
                ['label' => 'Brand', 'value' => 'Brand Name 1', 'print_value' => 'Brand Name 1'],
                ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter'],
            ],
            $this->invokeMethod(
                $this->abstractItemOptionsPlugin,
                'addOptionsToOrderItemOptions',
                [$orderItem, $initialOptions, false]
            )
        );
    }

    public function testAddOptionsToOrderItemOptionsAllFilledAndEnabledOrderBrandMissing()
    {
        $sellerId = 5;
        $brandId = 105;
        $storeId = 1;

        $orderItem = $this->objectManager->getObject(\Magento\Sales\Model\Order\Item::class);
        $orderItem->setData(
            [
                OrderItemInterfaceSeller::SELLER_NAME            => 'Seller Name 1',
                OrderItemInterfaceSeller::SELLER_ID              => $sellerId,
                OrderItemInterfaceSeller::SELLER_BUSINESS_NUMBER => 'ABN1',
            ]
        );

        $this->marketplacerDataHelperMock->method('displaySellerBusinessNumber')->willReturn(true);
        $this->marketplacerDataHelperMock->method('displayBrandName')->willReturn(true);
        $this->marketplacerDataHelperMock->method('getTitleForSellerBusinessNumber')->willReturn('ABN Title');

        $this->sellerRepositoryMock->method('getById')->with($sellerId, $storeId)->willReturn($this->getSellerWithId5());

        $initialOptions = [
            ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter']
        ];
        $this->assertEquals(
            [
                ['label' => 'Seller', 'value' => 'Seller Name 1', 'print_value' => 'Seller Name 1'],
                ['label' => 'ABN Title', 'value' => 'ABN1', 'print_value' => 'ABN1'],
                ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter'],
            ],
            $this->invokeMethod(
                $this->abstractItemOptionsPlugin,
                'addOptionsToOrderItemOptions',
                [$orderItem, $initialOptions, false]
            )
        );
    }

    public function testAddOptionsToOrderItemOptionsAllFilledAndEnabledWithUrl()
    {
        $sellerId = 5;
        $brandId = 105;
        $storeId = 1;

        $orderItem = $this->objectManager->getObject(\Magento\Sales\Model\Order\Item::class);
        $orderItem->setData(
            [
                'store_id' => $storeId,
                OrderItemInterfaceSeller::SELLER_NAME            => 'Seller Name 1',
                OrderItemInterfaceSeller::SELLER_ID              => $sellerId,
                OrderItemInterfaceSeller::SELLER_BUSINESS_NUMBER => 'ABN1',
                OrderItemInterfaceBrand::BRAND_NAME              => 'Brand Name 1'
            ]
        );

        $this->marketplacerDataHelperMock->method('displaySellerBusinessNumber')->willReturn(true);
        $this->marketplacerDataHelperMock->method('displayBrandName')->willReturn(true);
        $this->marketplacerDataHelperMock->method('getTitleForSellerBusinessNumber')->willReturn('ABN Title');

        $seller5 = $this->getSellerWithId5();
        $this->sellerRepositoryMock->method('getById')->with($sellerId, $storeId)->willReturn($this->getSellerWithId5());

        $this->sellerUrlMock->method('getSellerUrl')->with($seller5)->willReturn('https://test-seller.url');

        $initialOptions = [
            ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter']
        ];
        $this->assertEquals(
            [
                ['label' => 'Seller', 'value' => '<a href="https://test-seller.url" target="_blank">Seller Name 1</a>', 'print_value' => 'Seller Name 1'],
                ['label' => 'ABN Title', 'value' => 'ABN1', 'print_value' => 'ABN1'],
                ['label' => 'Brand', 'value' => 'Brand Name 1', 'print_value' => 'Brand Name 1'],
                ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter'],
            ],
            $this->invokeMethod(
                $this->abstractItemOptionsPlugin,
                'addOptionsToOrderItemOptions',
                [$orderItem, $initialOptions, true]
            )
        );
    }


    /**
     * @return object
     */
    private function getProductMockWithSellerIdAndBrandId($sellerId = null, $brandId = null)
    {
        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);

        $productMock
            ->expects($this->any())
            ->method('getData')
            ->withConsecutive([$this->sellerAttributeCode], [$this->brandAttributeCode])
            ->willReturnOnConsecutiveCalls($sellerId, $brandId);

        return $productMock;
    }

    /**
     * @return object
     */
    private function getSellerWithId5() {
        $seller5 = $this->objectManager->getObject(\Marketplacer\SellerApi\Model\MarketplacerSeller::class);
        $seller5->setData([
            MarketplacerSellerInterface::SELLER_ID => 5,
            MarketplacerSellerInterface::NAME => 'Name 1',
            MarketplacerSellerInterface::BUSINESS_NUMBER => 'ABN1',
            'is_enabled' => true
        ]);

        return $seller5;
    }

    /**
     * @return object
     */
    private function getSellerWithId10() {
        $seller10 = $this->objectManager->getObject(\Marketplacer\SellerApi\Model\MarketplacerSeller::class);
        $seller10->setData([
            MarketplacerSellerInterface::SELLER_ID => 10,
            MarketplacerSellerInterface::NAME => 'Name 2',
            MarketplacerSellerInterface::BUSINESS_NUMBER => 'ABN2'
        ]);

        return $seller10;
    }
}
