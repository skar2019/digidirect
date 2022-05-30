<?php

namespace Marketplacer\BrandApi\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Base\Api\CacheInvalidatorInterface;
use Marketplacer\BrandApi\Model\BrandManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BrandManagementTest extends TestCase
{
    /**
     * @var \Marketplacer\BrandApi\Api\BrandRepositoryInterface|MockObject
     */
    private $brandRepositoryMock;

    /**
     * @var \Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterfaceFactory|MockObject
     */
    private $brandFactoryMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var CacheInvalidatorInterface|MockObject
     */
    private $cacheInvalidatorMock;

    /**
     * @var BrandManagement
     */
    private $brandManagement;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->brandRepositoryMock = $this->createMock(\Marketplacer\BrandApi\Model\Stubs\BrandRepositoryStub::class);
        $this->brandFactoryMock = $this->createMock(\Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterfaceFactory::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->cacheInvalidatorMock = $this->createMock(\Marketplacer\Base\Model\CacheInvalidator::class);

        $this->brandManagement = $this->objectManager->getObject(
            \Marketplacer\BrandApi\Model\BrandManagement::class,
            [
                'brandRepository' => $this->brandRepositoryMock,
                'brandFactory'    => $this->brandFactoryMock,
                'storeManager'     => $this->storeManagerMock,
                'cacheInvalidator' => $this->cacheInvalidatorMock,
            ]
        );
    }

    public function testGetIdByStore()
    {
        $brandId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Brand';

        $this->prepareStoreManagerMocks($requestedStoreId);

        $brand = $this->objectManager->getObject(\Marketplacer\BrandApi\Model\MarketplacerBrand::class);
        $brand
            ->setOptionId($optionId)
            ->setRowId($rowId)
            ->setBrandId($brandId)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->brandRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->willReturn($brand);

        $this->assertEquals($brand, $this->brandManagement->getById($brandId, $requestedStoreId));
    }

    public function testGetIdGlobal()
    {
        $brandId = 5;
        $optionId = 5;
        $requestedStoreId = Store::DEFAULT_STORE_ID;
        $rowId = 10;
        $name = 'Test Brand';

        $this->prepareStoreManagerMocks($requestedStoreId);

        $brand = $this->objectManager->getObject(\Marketplacer\BrandApi\Model\MarketplacerBrand::class);
        $brand
            ->setOptionId($optionId)
            ->setRowId($rowId)
            ->setBrandId($brandId)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->brandRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->willReturn($brand);

        $this->assertEquals($brand, $this->brandManagement->getById($brandId, $requestedStoreId));
    }

    public function testGetList()
    {
        $searchCriteria = $this->objectManager->getObject(\Magento\Framework\Api\SearchCriteria::class);

        $searchResults = $this->objectManager->getObject(\Marketplacer\BrandApi\Model\MarketplacerBrandSearchResults::class);
        $searchResults->setItems([]);

        $this->brandRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResults);

        $this->assertEquals($searchResults, $this->brandManagement->getList($searchCriteria));
    }

    public function testSaveNew()
    {
        $brandId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Brand';

        $this->prepareStoreManagerMocks($requestedStoreId);

        $createdEmptyBrand = $this->objectManager->getObject(\Marketplacer\BrandApi\Model\MarketplacerBrand::class);

        $resultBrand = $this->objectManager->getObject(\Marketplacer\BrandApi\Model\MarketplacerBrand::class);
        $resultBrand
            ->setOptionId($optionId)
            ->setRowId($rowId)
            ->setBrandId($brandId)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->brandRepositoryMock
            ->expects($this->never())
            ->method('getById');
        $this->brandFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($createdEmptyBrand);

        $this->brandRepositoryMock
            ->expects($this->once())
            ->method('save');

        $this->assertEquals($resultBrand, $this->brandManagement->save($resultBrand, null, $requestedStoreId));
    }

    public function testSaveExisting()
    {
        $brandId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Brand';

        $this->prepareStoreManagerMocks($requestedStoreId);

        $resultBrand = $this->objectManager->getObject(\Marketplacer\BrandApi\Model\MarketplacerBrand::class);
        $resultBrand
            ->setOptionId($optionId)
            ->setRowId($rowId)
            ->setBrandId($brandId)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->brandRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->willReturn($resultBrand);
        $this->brandFactoryMock
            ->expects($this->never())
            ->method('create');

        $savedBrand = $this->objectManager->getObject(\Marketplacer\BrandApi\Model\MarketplacerBrand::class);
        $savedBrand
            ->setRowId()
            ->setBrandId($brandId)
            ->setName($name);

        $this->brandRepositoryMock
            ->expects($this->once())
            ->method('save');

        $this->assertEquals($resultBrand, $this->brandManagement->save($resultBrand, $brandId, $requestedStoreId));
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
        $brandId = 5;

        $this->brandRepositoryMock
            ->expects($this->once())
            ->method('deleteById')
            ->willReturn(true);

        $this->assertTrue($this->brandManagement->deleteById($brandId));
    }

    public function testDeleteMissing()
    {
        $brandId = 0;

        $this->brandRepositoryMock
            ->expects($this->once())
            ->method('deleteById')
            ->willThrowException(new LocalizedException(__('')));

        $this->expectException(LocalizedException::class);

        $this->assertTrue($this->brandManagement->deleteById($brandId));
    }
}
