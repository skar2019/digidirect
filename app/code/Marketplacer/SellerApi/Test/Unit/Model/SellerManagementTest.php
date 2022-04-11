<?php

namespace Marketplacer\SellerApi\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Base\Api\CacheInvalidatorInterface;
use Marketplacer\SellerApi\Model\SellerManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellerManagementTest extends TestCase
{
    /**
     * @var \Marketplacer\SellerApi\Api\SellerRepositoryInterface|MockObject
     */
    private $sellerRepositoryMock;

    /**
     * @var \Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterfaceFactory|MockObject
     */
    private $sellerFactoryMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var CacheInvalidatorInterface|MockObject
     */
    private $cacheInvalidatorMock;

    /**
     * @var SellerManagement
     */
    private $sellerManagement;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->sellerRepositoryMock = $this->createMock(\Marketplacer\SellerApi\Model\Stubs\SellerRepositoryStub::class);
        $this->sellerFactoryMock = $this->createMock(\Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterfaceFactory::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->cacheInvalidatorMock = $this->createMock(\Marketplacer\Base\Model\CacheInvalidator::class);

        $this->sellerManagement = $this->objectManager->getObject(
            \Marketplacer\SellerApi\Model\SellerManagement::class,
            [
                'sellerRepository' => $this->sellerRepositoryMock,
                'sellerFactory'    => $this->sellerFactoryMock,
                'storeManager'     => $this->storeManagerMock,
                'cacheInvalidator' => $this->cacheInvalidatorMock,
            ]
        );
    }

    public function testGetIdByStore()
    {
        $sellerId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Seller';

        $this->prepareStoreManagerMocks($requestedStoreId);

        $seller = $this->objectManager->getObject(\Marketplacer\SellerApi\Model\MarketplacerSeller::class);
        $seller
            ->setOptionId($optionId)
            ->setRowId($rowId)
            ->setSellerId($sellerId)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->sellerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->willReturn($seller);

        $this->assertEquals($seller, $this->sellerManagement->getById($sellerId, $requestedStoreId));
    }

    public function testGetIdGlobal()
    {
        $sellerId = 5;
        $optionId = 5;
        $requestedStoreId = Store::DEFAULT_STORE_ID;
        $rowId = 10;
        $name = 'Test Seller';

        $this->prepareStoreManagerMocks($requestedStoreId);

        $seller = $this->objectManager->getObject(\Marketplacer\SellerApi\Model\MarketplacerSeller::class);
        $seller
            ->setOptionId($optionId)
            ->setRowId($rowId)
            ->setSellerId($sellerId)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->sellerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->willReturn($seller);

        $this->assertEquals($seller, $this->sellerManagement->getById($sellerId, $requestedStoreId));
    }

    public function testGetList()
    {
        $searchCriteria = $this->objectManager->getObject(\Magento\Framework\Api\SearchCriteria::class);

        $searchResults = $this->objectManager->getObject(\Marketplacer\SellerApi\Model\MarketplacerSellerSearchResults::class);
        $searchResults->setItems([]);

        $this->sellerRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResults);

        $this->assertEquals($searchResults, $this->sellerManagement->getList($searchCriteria));
    }

    public function testSaveNew()
    {
        $sellerId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Seller';

        $this->prepareStoreManagerMocks($requestedStoreId);

        $createdEmptySeller = $this->objectManager->getObject(\Marketplacer\SellerApi\Model\MarketplacerSeller::class);

        $resultSeller = $this->objectManager->getObject(\Marketplacer\SellerApi\Model\MarketplacerSeller::class);
        $resultSeller
            ->setOptionId($optionId)
            ->setRowId($rowId)
            ->setSellerId($sellerId)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->sellerRepositoryMock
            ->expects($this->never())
            ->method('getById');
        $this->sellerFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($createdEmptySeller);

        $this->sellerRepositoryMock
            ->expects($this->once())
            ->method('save');

        $this->assertEquals($resultSeller, $this->sellerManagement->save($resultSeller, null, $requestedStoreId));
    }

    public function testSaveExisting()
    {
        $sellerId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Seller';

        $this->prepareStoreManagerMocks($requestedStoreId);

        $resultSeller = $this->objectManager->getObject(\Marketplacer\SellerApi\Model\MarketplacerSeller::class);
        $resultSeller
            ->setOptionId($optionId)
            ->setRowId($rowId)
            ->setSellerId($sellerId)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->sellerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->willReturn($resultSeller);
        $this->sellerFactoryMock
            ->expects($this->never())
            ->method('create');

        $savedSeller = $this->objectManager->getObject(\Marketplacer\SellerApi\Model\MarketplacerSeller::class);
        $savedSeller
            ->setRowId()
            ->setSellerId($sellerId)
            ->setName($name);

        $this->sellerRepositoryMock
            ->expects($this->once())
            ->method('save');

        $this->assertEquals($resultSeller, $this->sellerManagement->save($resultSeller, $sellerId, $requestedStoreId));
    }

    /**
     * @param $storeId
     * @return void
     */
    private function prepareStoreManagerMocks($storeId = null)
    {
        if (null !== $storeId) {
            $this->storeManagerMock
                ->expects($this->never())
                ->method('getStore');
        } else {
            $store = $this->objectManager->getObject(Store::class);
            $store->setId($storeId);

            $this->storeManagerMock
                ->expects($this->once())
                ->method('getStore')
                ->willReturn($store);
        }
    }

    public function testDeleteExisting()
    {
        $sellerId = 5;

        $this->sellerRepositoryMock
            ->expects($this->once())
            ->method('deleteById')
            ->willReturn(true);

        $this->assertTrue($this->sellerManagement->deleteById($sellerId));
    }

    public function testDeleteMissing()
    {
        $sellerId = 0;

        $this->sellerRepositoryMock
            ->expects($this->once())
            ->method('deleteById')
            ->willThrowException(new LocalizedException(__('')));

        $this->expectException(LocalizedException::class);

        $this->assertTrue($this->sellerManagement->deleteById($sellerId));
    }
}
