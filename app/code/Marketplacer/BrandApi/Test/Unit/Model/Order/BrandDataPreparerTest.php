<?php

namespace Marketplacer\BrandApi\Test\Unit\Model\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BrandDataPreparerTest extends TestCase
{
    /**
     * @var \Marketplacer\BrandApi\Api\BrandRepositoryInterface|MockObject
     */
    private $brandRepositoryMock;

    /**
     * @var BrandAttributeRetrieverInterface|MockObject
     */
    private $brandAttributeRetrieverMock;

    /**
     * @var \Marketplacer\BrandApi\Model\Order\BrandDataPreparer
     */
    private $brandDataPreparer;

    /**
     * @var string
     */
    private $brandAttributeCode = 'marketplacer_brand';

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->brandRepositoryMock = $this->createMock(\Marketplacer\BrandApi\Model\Stubs\BrandRepositoryStub::class);
        $this->brandAttributeRetrieverMock = $this->createMock(BrandAttributeRetrieverInterface::class);
        $this->brandAttributeRetrieverMock->method('getAttributeCode')->willReturn($this->brandAttributeCode);

        $this->brandDataPreparer = $this->objectManager->getObject(
            \Marketplacer\BrandApi\Model\Order\BrandDataPreparer::class,
            [
                'brandRepository' => $this->brandRepositoryMock,
                'brandAttributeRetriever' => $this->brandAttributeRetrieverMock,
            ]
        );
    }

    public function testGetBrandNameByIdExisting()
    {
        $brandId = 5;
        $requestedStoreId = 1;
        $result = 'Name 1';

        $this->brandRepositoryMock
            ->method('getById')
            ->with(5, 1)
            ->willReturn($this->getBrandWithId5());

        $this->assertEquals($result, $this->brandDataPreparer->getBrandNameById($brandId, $requestedStoreId));
    }

    public function testGetBrandNameByIdMissing()
    {
        $brandId = 5;
        $requestedStoreId = 1;

        $this->brandRepositoryMock
            ->method('getById')
            ->with(5, 1)
            ->will($this->throwException(new NoSuchEntityException()));

        $this->assertNull($this->brandDataPreparer->getBrandNameById($brandId, $requestedStoreId));
    }

    public function testGetBrandIdByQuoteItemExisting()
    {
        $brandId = 5;

        $quoteItemMock = $this->createMock(\Magento\Quote\Model\Quote\Item::class);

        $quoteItemMock
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock = $this->createMock(\Magento\Catalog\Model\Product::class));
        $productMock
            ->expects($this->once())
            ->method('getData')
            ->with($this->brandAttributeCode)
            ->willReturn($brandId);

        $this->assertEquals($brandId, $this->brandDataPreparer->getBrandIdByQuoteItem($quoteItemMock));
    }

    public function testGetBrandIdByQuoteItemEmpty()
    {
        $brandId = 5;

        $quoteItemMock = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $quoteItemMock
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock = $this->createMock(\Magento\Catalog\Model\Product::class));
        $productMock
            ->expects($this->once())
            ->method('getData')
            ->with($this->brandAttributeCode)
            ->willReturn(null);

        $this->assertNull($this->brandDataPreparer->getBrandIdByQuoteItem($quoteItemMock));
    }

    public function testGetBrandIdsByQuote()
    {
        $brandIds = [5, 10];
        $resulBrandIds = [5 => 5];

        $quoteItemMock1 = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $quoteItemMock1
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock1 = $this->createMock(\Magento\Catalog\Model\Product::class));
        $productMock1
            ->expects($this->once())
            ->method('getData')
            ->with($this->brandAttributeCode)
            ->willReturn(5);

        $quoteItemMock2 = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $quoteItemMock2
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock2 = $this->createMock(\Magento\Catalog\Model\Product::class));
        $productMock2
            ->expects($this->once())
            ->method('getData')
            ->with($this->brandAttributeCode)
            ->willReturn(null);

        $addressItemMock = $this->createMock(\Magento\Quote\Model\Quote\Address\Item::class);
        $addressItemMock
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock3 = $this->createMock(\Magento\Catalog\Model\Product::class));
        $productMock3
            ->expects($this->once())
            ->method('getData')
            ->with($this->brandAttributeCode)
            ->willReturn(null);

        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quote
            ->expects($this->once())
            ->method('getAllVisibleItems')
            ->willReturn([$quoteItemMock1, $quoteItemMock2, $addressItemMock]);

        $this->assertEquals($resulBrandIds, $this->brandDataPreparer->getBrandIdsByQuote($quote));
    }

    public function testGetBrandNamesByQuoteItems()
    {
        $resulBrandNames = 'Name 1';

        $quoteItemMock1 = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $quoteItemMock1
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock1 = $this->createMock(\Magento\Catalog\Model\Product::class));
        $productMock1
            ->expects($this->once())
            ->method('getData')
            ->with($this->brandAttributeCode)
            ->willReturn(5);

        $quoteItemMock2 = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $quoteItemMock2
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock2 = $this->createMock(\Magento\Catalog\Model\Product::class));
        $productMock2
            ->expects($this->once())
            ->method('getData')
            ->with($this->brandAttributeCode)
            ->willReturn(null);

        $quoteItemMock3 = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $quoteItemMock3
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock3 = $this->createMock(\Magento\Catalog\Model\Product::class));
        $productMock3
            ->expects($this->once())
            ->method('getData')
            ->with($this->brandAttributeCode)
            ->willReturn(10);

        $this->brandRepositoryMock
            ->method('getById')
            ->withConsecutive([5, 1], [10, 1])
            ->willReturnOnConsecutiveCalls(
                $this->getBrandWithId5(),
                $this->throwException(new NoSuchEntityException())
            );

        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quote
            ->expects($this->once())
            ->method('getAllVisibleItems')
            ->willReturn([$quoteItemMock1, $quoteItemMock2, $quoteItemMock3]);
        $quote
            ->expects($this->exactly(2))
            ->method('getStoreId')
            ->willReturn(1);

        $this->assertEquals(
            $resulBrandNames,
            $this->brandDataPreparer->getBrandNamesByQuoteItems(
                $quote
            )
        );
    }

    /**
     * @return object
     */
    private function getBrandWithId5() {
        $brand5 = $this->objectManager->getObject(\Marketplacer\BrandApi\Model\MarketplacerBrand::class);
        $brand5->setData([
            MarketplacerBrandInterface::BRAND_ID => 5,
            MarketplacerBrandInterface::NAME => 'Name 1',
        ]);

        return $brand5;
    }

    /**
     * @return object
     */
    private function getBrandWithId10() {
        $brand10 = $this->objectManager->getObject(\Marketplacer\BrandApi\Model\MarketplacerBrand::class);
        $brand10->setData([
            MarketplacerBrandInterface::BRAND_ID => 10,
            MarketplacerBrandInterface::NAME => 'Name 2',
        ]);

        return $brand10;
    }
}
