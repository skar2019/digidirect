<?php

namespace Marketplacer\SellerApi\Test\Unit\Model\Order;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface;
use Marketplacer\SellerApi\Api\Data\OrderItemInterface;
use Marketplacer\SellerApi\Api\SellerAttributeRetrieverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellerDataPreparerTest extends TestCase
{
    /**
     * @var \Marketplacer\SellerApi\Api\SellerRepositoryInterface|MockObject
     */
    private $sellerRepositoryMock;

    /**
     * @var SellerAttributeRetrieverInterface|MockObject
     */
    private $sellerAttributeRetrieverMock;

    /**
     * @var ProductRepositoryInterface|MockObject
     */
    private $productRepositoryMock;

    /**
     * @var \Marketplacer\SellerApi\Model\Order\SellerDataPreparer
     */
    private $sellerDataPreparer;

    /**
     * @var string
     */
    private $sellerAttributeCode = 'marketplacer_seller';

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->sellerRepositoryMock = $this->createMock(\Marketplacer\SellerApi\Model\Stubs\SellerRepositoryStub::class);
        $this->sellerAttributeRetrieverMock = $this->createMock(SellerAttributeRetrieverInterface::class);
        $this->sellerAttributeRetrieverMock->method('getAttributeCode')->willReturn($this->sellerAttributeCode);

        $this->productRepositoryMock = $this->createMock(\Magento\Catalog\Model\ProductRepository::class);

        $this->productMock = $this->createMock(\Magento\Catalog\Model\Product::class);

        $this->sellerDataPreparer = $this->objectManager->getObject(
            \Marketplacer\SellerApi\Model\Order\SellerDataPreparer::class,
            [
                'sellerRepository' => $this->sellerRepositoryMock,
                'sellerAttributeRetriever' => $this->sellerAttributeRetrieverMock,
                'productRepository' => $this->productRepositoryMock,
            ]
        );
    }

    public function testGetSellerNamesByIdsAllExists()
    {
        $sellerIds = [5, 10];
        $requestedStoreId = 1;
        $result = ['Name 1', 'Name 2'];

        $this->sellerRepositoryMock
            ->method('getById')
            ->withConsecutive([5, 1], [10, 1])
            ->willReturnOnConsecutiveCalls($this->getSellerWithId5(), $this->getSellerWithId10());

        $this->assertEquals($result, $this->sellerDataPreparer->getSellerNamesByIds($sellerIds, $requestedStoreId));
    }

    public function testGetSellerNamesByIdsOneMissing()
    {
        $sellerIds = [5, 7, 10];
        $requestedStoreId = 1;
        $result = ['Name 1', 'Name 2'];

        $this->sellerRepositoryMock
            ->method('getById')
            ->withConsecutive([5, 1], [7, 1], [10, 1])
            ->willReturnOnConsecutiveCalls(
                $this->getSellerWithId5(),
                $this->throwException(new NoSuchEntityException()),
                $this->getSellerWithId10()
            );

        $this->assertEquals($result, $this->sellerDataPreparer->getSellerNamesByIds($sellerIds, $requestedStoreId));
    }

    public function testGetSellerNameByIdExisting()
    {
        $sellerId = 5;
        $requestedStoreId = 1;
        $result = 'Name 1';

        $this->sellerRepositoryMock
            ->method('getById')
            ->with(5, 1)
            ->willReturn($this->getSellerWithId5());

        $this->assertEquals($result, $this->sellerDataPreparer->getSellerNameById($sellerId, $requestedStoreId));
    }

    public function testGetSellerNameByIdMissing()
    {
        $sellerId = 5;
        $requestedStoreId = 1;

        $this->sellerRepositoryMock
            ->method('getById')
            ->with(5, 1)
            ->will($this->throwException(new NoSuchEntityException()));

        $this->assertNull($this->sellerDataPreparer->getSellerNameById($sellerId, $requestedStoreId));
    }

    public function testGetSellerBusinessNumberByIdExisting()
    {
        $sellerId = 5;
        $requestedStoreId = 1;
        $result = 'ABN1';

        $this->sellerRepositoryMock
            ->method('getById')
            ->with(5, 1)
            ->willReturn($this->getSellerWithId5());

        $this->assertEquals($result, $this->sellerDataPreparer->getSellerBusinessNumberById($sellerId, $requestedStoreId));
    }

    public function testGetSellerBusinessNumberByIdMissing()
    {
        $sellerId = 5;
        $requestedStoreId = 1;

        $this->sellerRepositoryMock
            ->method('getById')
            ->with(5, 1)
            ->will($this->throwException(new NoSuchEntityException()));

        $this->assertNull($this->sellerDataPreparer->getSellerBusinessNumberById($sellerId, $requestedStoreId));
    }

    public function testGetSellerIdsByQuoteItems()
    {
        $sellerIds = [5, 10];
        $resulSellerIds = [5 => 5];

        $quoteItemMock1 = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $quoteItemMock1
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock1 = $this->createMock(\Magento\Catalog\Model\Product::class));
        $productMock1
            ->expects($this->once())
            ->method('getData')
            ->with($this->sellerAttributeCode)
            ->willReturn(5);

        $quoteItemMock2 = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $quoteItemMock2
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock2 = $this->createMock(\Magento\Catalog\Model\Product::class));
        $productMock2
            ->expects($this->once())
            ->method('getData')
            ->with($this->sellerAttributeCode)
            ->willReturn(null);

        $this->assertEquals($resulSellerIds, $this->sellerDataPreparer->getSellerIdsByQuoteItems([$quoteItemMock1, $quoteItemMock2]));
    }

    public function testGetSellerIdByQuoteItem()
    {
        $sellerId = 5;

        $quoteItemMock = $this->createMock(\Magento\Quote\Model\Quote\Item::class);

        $quoteItemMock
            ->expects($this->once())
            ->method('getProduct')
            ->willReturn($this->productMock);
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with($this->sellerAttributeCode)
            ->willReturn($sellerId);

        $this->assertEquals($sellerId, $this->sellerDataPreparer->getSellerIdByQuoteItem($quoteItemMock));
    }

    public function testGetSellerNamesBySalesItems()
    {
        $resultSellerNames = [
            5 => 'Test Invoice Seller 1',
            10 => 'Test Shipment Seller 2',
            20 => 'Test Order Seller 4',
        ];

        $invoiceItemMock = $this->createMock(\Magento\Sales\Model\Order\Invoice\Item::class);
        $invoiceItemMock
            ->expects($this->exactly(2))
            ->method('getOrderItem')
            ->willReturn($orderItemMock1 = $this->createMock(\Magento\Sales\Model\Order\Item::class));
        $orderItemMock1
            ->expects($this->exactly(2))
            ->method('getData')
            ->withConsecutive([OrderItemInterface::SELLER_ID], [OrderItemInterface::SELLER_NAME])
            ->willReturnOnConsecutiveCalls(5, 'Test Invoice Seller 1');

        $shipmentItemMock = $this->createMock(\Magento\Sales\Model\Order\Shipment\Item::class);
        $shipmentItemMock
            ->expects($this->exactly(2))
            ->method('getOrderItem')
            ->willReturn($orderItemMock2 = $this->createMock(\Magento\Sales\Model\Order\Item::class));
        $orderItemMock2
            ->expects($this->exactly(2))
            ->method('getData')
            ->withConsecutive([OrderItemInterface::SELLER_ID], [OrderItemInterface::SELLER_NAME])
            ->willReturnOnConsecutiveCalls(10, 'Test Shipment Seller 2');

        $orderItemMock3 = $this->createMock(\Magento\Sales\Model\Order\Item::class);
        $orderItemMock3
            ->expects($this->once())
            ->method('getData')
            ->with(OrderItemInterface::SELLER_ID)
            ->willReturn(null);

        $orderItemMock4 = $this->createMock(\Magento\Sales\Model\Order\Item::class);
        $orderItemMock4
            ->expects($this->exactly(2))
            ->method('getData')
            ->withConsecutive([OrderItemInterface::SELLER_ID], [OrderItemInterface::SELLER_NAME])
            ->willReturnOnConsecutiveCalls(20, 'Test Order Seller 4');

        $addressItemMock = $this->createMock(\Magento\Quote\Model\Quote\Address\Item::class);

        $this->assertEquals(
            $resultSellerNames,
            $this->sellerDataPreparer->getSellerNamesBySalesItems(
                [$invoiceItemMock, $shipmentItemMock, $orderItemMock3, $orderItemMock4, $addressItemMock]
            )
        );
    }

    public function testGetSellerBusinessNumbersBySalesItems()
    {
        $resulSellerNames = [
            5 => 'Test Invoice ABN 1',
            10 => 'Test Shipment ABN 2',
            20 => 'Test Order ABN 4',
        ];

        $invoiceItemMock = $this->createMock(\Magento\Sales\Model\Order\Invoice\Item::class);
        $invoiceItemMock
            ->expects($this->exactly(2))
            ->method('getOrderItem')
            ->willReturn($orderItemMock1 = $this->createMock(\Magento\Sales\Model\Order\Item::class));
        $orderItemMock1
            ->expects($this->exactly(2))
            ->method('getData')
            ->withConsecutive([OrderItemInterface::SELLER_ID], [OrderItemInterface::SELLER_BUSINESS_NUMBER])
            ->willReturnOnConsecutiveCalls(5, 'Test Invoice ABN 1');

        $shipmentItemMock = $this->createMock(\Magento\Sales\Model\Order\Shipment\Item::class);
        $shipmentItemMock
            ->expects($this->exactly(2))
            ->method('getOrderItem')
            ->willReturn($orderItemMock2 = $this->createMock(\Magento\Sales\Model\Order\Item::class));
        $orderItemMock2
            ->expects($this->exactly(2))
            ->method('getData')
            ->withConsecutive([OrderItemInterface::SELLER_ID], [OrderItemInterface::SELLER_BUSINESS_NUMBER])
            ->willReturnOnConsecutiveCalls(10, 'Test Shipment ABN 2');

        $orderItemMock3 = $this->createMock(\Magento\Sales\Model\Order\Item::class);
        $orderItemMock3
            ->expects($this->once())
            ->method('getData')
            ->with(OrderItemInterface::SELLER_ID)
            ->willReturn(null);

        $orderItemMock4 = $this->createMock(\Magento\Sales\Model\Order\Item::class);
        $orderItemMock4
            ->expects($this->exactly(2))
            ->method('getData')
            ->withConsecutive([OrderItemInterface::SELLER_ID], [OrderItemInterface::SELLER_BUSINESS_NUMBER])
            ->willReturnOnConsecutiveCalls(20, 'Test Order ABN 4');

        $addressItemMock = $this->createMock(\Magento\Quote\Model\Quote\Address\Item::class);
        $addressItemMock
            ->expects($this->never())
            ->method('getData');

        $this->assertEquals(
            $resulSellerNames,
            $this->sellerDataPreparer->getSellerBusinessNumbersBySalesItems(
                [$invoiceItemMock, $shipmentItemMock, $orderItemMock3, $orderItemMock4, $addressItemMock]
            )
        );
    }

    /**
     * @return object
     */
    private function getSellerWithId5() {
        $seller5 = $this->objectManager->getObject(\Marketplacer\SellerApi\Model\MarketplacerSeller::class);
        $seller5->setData([
            MarketplacerSellerInterface::SELLER_ID => 5,
            MarketplacerSellerInterface::NAME => 'Name 1',
            MarketplacerSellerInterface::BUSINESS_NUMBER => 'ABN1'
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
