<?php

namespace Marketplacer\Marketplacer\Test\Unit\Plugin\Sales\Block;

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

        $this->abstractItemOptionsPlugin = $this->objectManager->getObject(
            \Marketplacer\Marketplacer\Test\Unit\Plugin\Sales\Block\TestedItemOptionsPlugin::class,
            [
                'marketplacerDataHelper'   => $this->marketplacerDataHelperMock,
            ]
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

        $initialOptions = [
            ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter']
        ];
        $this->assertEquals(
            [
                ['label' => 'Brand', 'value' => 'Brand Name 1', 'print_value' => 'Brand Name 1'],
                ['label' => 'Seller', 'value' => 'Seller Name 1', 'print_value' => 'Seller Name 1'],
                ['label' => 'ABN Title', 'value' => 'ABN1', 'print_value' => 'ABN1'],
                ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter'],
            ],
            $this->invokeMethod(
                $this->abstractItemOptionsPlugin,
                'addOptions',
                [$orderItem, $initialOptions]
            )
        );
    }

    public function testAddOptionsToOrderItemOptionsAllFilledAndEnabledWithBrandConfigDisabled()
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
        $this->marketplacerDataHelperMock->method('displayBrandName')->willReturn(false);
        $this->marketplacerDataHelperMock->method('getTitleForSellerBusinessNumber')->willReturn('ABN Title');

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
                'addOptions',
                [$orderItem, $initialOptions]
            )
        );
    }

    public function testAddOptionsToOrderItemOptionsAllFilledAndEnabledWithAbnConfigDisabled()
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

        $this->marketplacerDataHelperMock->method('displaySellerBusinessNumber')->willReturn(false);
        $this->marketplacerDataHelperMock->method('displayBrandName')->willReturn(true);
        $this->marketplacerDataHelperMock->method('getTitleForSellerBusinessNumber')->willReturn('ABN Title');

        $initialOptions = [
            ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter']
        ];
        $this->assertEquals(
            [
                ['label' => 'Brand', 'value' => 'Brand Name 1', 'print_value' => 'Brand Name 1'],
                ['label' => 'Seller', 'value' => 'Seller Name 1', 'print_value' => 'Seller Name 1'],
                ['label' => 'Item option 1', 'value' => 'No matter', 'print_value' => 'No matter'],
            ],
            $this->invokeMethod(
                $this->abstractItemOptionsPlugin,
                'addOptions',
                [$orderItem, $initialOptions]
            )
        );
    }
}
