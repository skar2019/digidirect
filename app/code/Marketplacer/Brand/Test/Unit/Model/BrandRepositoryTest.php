<?php

namespace Marketplacer\Brand\Test\Unit\Model;

use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Base\Model\Attribute\AttributeOptionHandler;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Api\Data\BrandInterfaceFactory;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandSearchResultsInterfaceFactory;
use Marketplacer\Brand\Model\ResourceModel\Brand\Collection;
use Marketplacer\Brand\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;
use Marketplacer\Brand\Model\Brand;
use Marketplacer\Brand\Model\Brand\BrandDataToOptionSetter;
use Marketplacer\Brand\Model\Brand\Validator as BrandValidator;
use Marketplacer\Brand\Model\BrandRepository;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BrandRepositoryTest extends TestCase
{
    /**
     * @var \Marketplacer\Brand\Model\ResourceModel\Brand|MockObject
     */
    private $brandResourceMock;

    /**
     * @var BrandAttributeRetrieverInterface|MockObject
     */
    private $brandAttributeRetrieverMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var BrandInterfaceFactory|MockObject
     */
    private $brandFactoryMock;

    /**
     * @var BrandValidator|MockObject
     */
    private $brandValidatorMock;

    /**
     * @var AttributeOptionInterfaceFactory|MockObject
     */
    private $attributeOptionFactoryMock;

    /**
     * @var AttributeOptionHandler|MockObject
     */
    private $attributeOptionHandlerMock;

    /**
     * @var BrandCollectionFactory |MockObject
     */
    private $brandCollectionFactoryMock;

    /**
     * @var MarketplacerBrandSearchResultsInterfaceFactory | MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var CollectionProcessorInterface|MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Collection|MockObject
     */
    private $collectionMock;

    /**
     * @var BrandRepository|MockObject
     */
    private $repository;

    /**
     * @var Brand|mixed|MockObject
     */
    private $brandMock;

    /**
     * @var BrandDataToOptionSetter
     */
    private $brandDataToOptionSetter;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->brandDataToOptionSetter = $this->objectManager->getObject(
            \Marketplacer\Brand\Model\Brand\BrandDataToOptionSetter::class,
            [
                'attributeOptionLabelFactory' => $this->createConfiguredMock(
                    \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory::class,
                    [
                        'create' => $this->objectManager->getObject(\Magento\Eav\Model\Entity\Attribute\OptionLabel::class)
                    ]
                )
            ]
        );

        $this->brandResourceMock = $this->createMock(\Marketplacer\Brand\Model\ResourceModel\Brand::class);
        $this->brandAttributeRetrieverMock = $this->createMock(BrandAttributeRetrieverInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->brandFactoryMock = $this->createMock(BrandInterfaceFactory::class);
        $this->brandValidatorMock = $this->createMock(BrandValidator::class);
        $this->attributeOptionFactoryMock = $this->createMock(AttributeOptionInterfaceFactory::class);
        $this->attributeOptionHandlerMock = $this->createMock(AttributeOptionHandler::class);
        $this->brandCollectionFactoryMock = $this->createMock(BrandCollectionFactory::class);
        $this->searchResultsFactoryMock = $this->createMock(MarketplacerBrandSearchResultsInterfaceFactory::class);
        $this->collectionProcessorMock = $this->createMock(CollectionProcessorInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->collectionMock = $this->createPartialMock(
            Collection::class,
            [
                'addBrandIdToFilter',
                'addStatusActiveToFilter',
                'addStoreIdToFilter',
                'setOrder',
                'setCurPage',
                'setPageSize',
                'getFirstItem',
                'getItems',
                'getFlag',
                'getIterator',
                'getSize'
            ]
        );

        $this->brandMock = $this->createMock(Brand::class);

        $this->repository = $this->objectManager->getObject(
            BrandRepository::class,
            [
                'brandResource'             => $this->brandResourceMock,
                'brandFactory'              => $this->brandFactoryMock,
                'attributeOptionHandler'     => $this->attributeOptionHandlerMock,
                'brandAttributeRetriever'   => $this->brandAttributeRetrieverMock,
                'storeManager'               => $this->storeManagerMock,
                'attributeOptionFactory'     => $this->attributeOptionFactoryMock,
                'brandCollectionFactory'    => $this->brandCollectionFactoryMock,
                'brandValidator'            => $this->brandValidatorMock,
                'brandDataToOptionSetter'   => $this->brandDataToOptionSetter,
                'brandSearchResultsFactory' => $this->searchResultsFactoryMock,
                'collectionProcessor'        => $this->collectionProcessorMock,
                'logger'                     => $this->loggerMock,
            ]
        );
    }

    public function testGetStoreViewRecordById(/** $brandData, $storeId*/)
    {
        $brandId = 5;
        $requestedStoreId = 1;
        $rowId = 5;

        $this->prepareMocksToTestGetById($requestedStoreId);

        $this->brandMock
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn($requestedStoreId);
        $this->brandMock
            ->expects($this->once())
            ->method('getRowId')
            ->willReturn($rowId);
        $this->brandMock
            ->expects($this->never())
            ->method('__call');
        $this->brandMock
            ->expects($this->never())
            ->method('setStoreId');

        $this->collectionMock
            ->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($this->brandMock);

        $this->repository->getById($brandId, $requestedStoreId);
    }

    public function testGetGlobalStoreViewRecordById(/** $brandData, $storeId*/)
    {
        $brandId = 5;
        $requestedStoreId = 1;
        $rowId = 5;

        $this->prepareMocksToTestGetById($requestedStoreId);

        $this->brandMock
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn(Store::DEFAULT_STORE_ID);
        $this->brandMock
            ->expects($this->once())
            ->method('getRowId')
            ->willReturn($rowId);
        $this->brandMock
            ->expects($this->once())
            ->method('__call')
            ->with('unsRowId')
            ->willReturnSelf();
        $this->brandMock
            ->expects($this->once())
            ->method('setStoreId')
            ->willReturn($rowId)
            ->with($requestedStoreId);

        $this->collectionMock
            ->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($this->brandMock);

        $this->repository->getById($brandId, $requestedStoreId);
    }

    public function testGetMissingBrandById(/** $brandData, $storeId*/)
    {
        $brandId = 5;
        $requestedStoreId = 1;
        $rowId = 5;

        $this->prepareMocksToTestGetById($requestedStoreId);

        $this->brandMock
            ->expects($this->never())
            ->method('getStoreId')
            ->willReturn(Store::DEFAULT_STORE_ID);
        $this->brandMock
            ->expects($this->once())
            ->method('getRowId')
            ->willReturn(null);

        $this->collectionMock
            ->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($this->brandMock);

        $this->expectException(NoSuchEntityException::class);

        $this->repository->getById($brandId, $requestedStoreId);
    }

    /**
     * @param $storeId
     * @return void
     */
    private function prepareMocksToTestGetById($storeId = null)
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

        $this->brandCollectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $this->collectionMock
            ->expects($this->once())
            ->method('addBrandIdToFilter')
            ->willReturnSelf();
        $this->collectionMock
            ->expects($this->once())
            ->method('addStoreIdToFilter')
            ->willReturnSelf();

        $this->collectionMock
            ->expects($this->once())
            ->method('setOrder')
            ->willReturnSelf();

        $this->collectionMock
            ->expects($this->once())
            ->method('setCurPage')
            ->willReturnSelf();
        $this->collectionMock
            ->expects($this->once())
            ->method('setCurPage')
            ->willReturnSelf();
        $this->collectionMock
            ->expects($this->once())
            ->method('setPageSize')
            ->willReturnSelf();
    }

    public function testGetAllDisplayedBrands()
    {
        $requestedStoreId = 1;

        $this->prepareMocksToTestBrandCollections($requestedStoreId);

        $this->collectionMock
            ->expects($this->once())
            ->method('addStoreIdToFilter')
            ->willReturnSelf();
        $this->collectionMock
            ->expects($this->once())
            ->method('addStatusActiveToFilter')
            ->willReturnSelf();

        $this->collectionMock
            ->expects($this->exactly(2))
            ->method('setOrder')
            ->willReturnSelf();

        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $this->repository->getAllDisplayedBrands($requestedStoreId);
    }

    public function testGetByIds()
    {
        $requestedStoreId = 1;

        $this->prepareMocksToTestBrandCollections($requestedStoreId);

        $this->collectionMock
            ->expects($this->once())
            ->method('addBrandIdToFilter')
            ->willReturnSelf();
        $this->collectionMock
            ->expects($this->once())
            ->method('addStoreIdToFilter')
            ->willReturnSelf();

        $this->collectionMock
            ->expects($this->exactly(2))
            ->method('setOrder')
            ->willReturnSelf();

        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $this->repository->getByIds([1,2], $requestedStoreId);
    }

    /**
     * @param $storeId
     * @return void
     */
    private function prepareMocksToTestBrandCollections($storeId = null)
    {
        $store = $this->objectManager->getObject(Store::class);
        $store->setId($storeId);

        if ($storeId) {
            $this->storeManagerMock
                ->expects($this->never())
                ->method('getStore');
        } else {
            $this->storeManagerMock
                ->expects($this->once())
                ->method('getStore')
                ->willReturn($store);
        }

        $this->brandCollectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testGetAllBrandIds()
    {
        $ids = [1, 3, 5, 10];

        $this->brandResourceMock
            ->expects($this->once())
            ->method('getAllBrandIds')
            ->willReturn($ids);

        $this->assertSame($ids, $this->repository->getAllBrandIds());
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testGetAllStoreRecordsByExistingId()
    {
        $brandId = 5;

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStores')
            ->with(true, false)
            ->willReturn(
                [
                    0 => $this->objectManager->getObject(Store::class)->setId(0),
                    1 => $this->objectManager->getObject(Store::class)->setId(1),
                ]
            );

        $this->brandCollectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $this->collectionMock
            ->expects($this->once())
            ->method('addBrandIdToFilter')
            ->willReturnSelf();
        $this->collectionMock
            ->expects($this->once())
            ->method('addStoreIdToFilter')
            ->willReturnSelf();

        $this->collectionMock
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(
                new \ArrayIterator(
                    [
                        0 => $this->objectManager->getObject(Brand::class)->setStoreId(0),
                        1 => $this->objectManager->getObject(Brand::class)->setStoreId(1),
                    ]
                )
            );

        $this->repository->getAllStoreRecordsById($brandId);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testGetAllStoreRecordsByMissingId()
    {
        $brandId = 5;

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStores')
            ->with(true, false)
            ->willReturn(
                [
                    0 => $this->objectManager->getObject(Store::class)->setId(0),
                    1 => $this->objectManager->getObject(Store::class)->setId(1),
                ]
            );

        $this->brandCollectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $this->collectionMock
            ->expects($this->once())
            ->method('addBrandIdToFilter')
            ->willReturnSelf();

        $this->collectionMock
            ->expects($this->once())
            ->method('addStoreIdToFilter')
            ->willReturnSelf();

        $this->collectionMock
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([]));

        $this->expectException(NoSuchEntityException::class);

        $this->repository->getAllStoreRecordsById($brandId);
    }


    public function testGetList()
    {
        /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->objectManager->getObject(\Magento\Framework\Api\SearchCriteria::class);
        $searchCriteria->setFilterGroups([]);

        $count = 2;
        $items = [
            1 => $this->objectManager->getObject(Brand::class),
            5 => $this->objectManager->getObject(Brand::class),
        ];

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->objectManager->getObject(Store::class)->setId(1));

        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->objectManager->getObject(\Marketplacer\BrandApi\Model\MarketplacerBrandSearchResults::class));

        $this->brandCollectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
        $this->collectionMock
            ->expects($this->once())
            ->method('getSize')
            ->willReturn($count);

        $searchResults = $this->repository->getList($searchCriteria);

        $this->assertEquals($count, $searchResults->getTotalCount());
        $this->assertEquals($searchCriteria, $searchResults->getSearchCriteria());
        $this->assertEquals($items, $searchResults->getBrands());
    }

    public function testSaveCreateNewValidBrandOnDefaultLevel()
    {
        $brandId = 5; // $brandId and $optionId must be equal with current implementation
        $optionId = 5;
        $requestedStoreId = Store::DEFAULT_STORE_ID;
        $rowId = 10;
        $name = 'Test Brand';

        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brand
            ->setOptionId(null)
            ->setRowId(null)
            ->setBrandId(null)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->brandValidatorMock->method('validate')->willReturn(null);

        $attributeMock = $this->objectManager->getObject(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->brandAttributeRetrieverMock->method('getAttribute')->willReturn($attributeMock);

        /** @var \Magento\Eav\Model\Entity\Attribute\Option $option */
        $option = $this->objectManager->getObject(\Magento\Eav\Model\Entity\Attribute\Option::class);
        $option->setIsDefault(0)->setSortOrder(0)->setValue(0);
        $this->attributeOptionHandlerMock->method('createAttributeOption')->willReturn($option);

        $this->attributeOptionHandlerMock->method('isAdminLabelUnique')->willReturn(true);

        $this->attributeOptionHandlerMock->method('saveAttributeOption')->willReturn($option);
        $option->setValue($optionId);

        $this->brandResourceMock->expects($this->once())->method('save')->willReturnSelf();

        $brand->setRowId($rowId);

        $this->repository->save($brand);

        $this->assertEquals($optionId, $brand->getOptionId());
        $this->assertEquals($rowId, $brand->getRowId());
        $this->assertEquals($brandId, $brand->getBrandId());
        $this->assertEquals('test-brand', $brand->getUrlKey());

    }

    public function testSaveCreateNewValidBrandOnStoreLevel()
    {
        $brandId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Brand';

        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brand
            ->setOptionId(null)
            ->setRowId(null)
            ->setBrandId(null)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->brandValidatorMock->method('validate')->willReturn(null);

        $attributeMock = $this->objectManager->getObject(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->brandAttributeRetrieverMock->method('getAttribute')->willReturn($attributeMock);

        /** @var \Magento\Eav\Model\Entity\Attribute\Option $option */
        $option = $this->objectManager->getObject(\Magento\Eav\Model\Entity\Attribute\Option::class);
        $option->setIsDefault(0)->setSortOrder(0)->setValue(0);
        $this->attributeOptionHandlerMock->method('createAttributeOption')->willReturn($option);

        $this->attributeOptionHandlerMock->method('isAdminLabelUnique')->willReturn(true);

        $this->attributeOptionHandlerMock->method('saveAttributeOption')->willReturn($option);
        $option->setValue($optionId);

        $this->brandResourceMock->expects($this->exactly(2))->method('save')->willReturnSelf();
        $brand->setRowId($rowId);

        $this->repository->save($brand);

        $this->assertEquals($optionId, $brand->getOptionId());
        $this->assertEquals($brandId, $brand->getBrandId());
        $this->assertEquals('test-brand', $brand->getUrlKey());
    }

    public function testSaveCreateBrandWithExistingOptionName()
    {
        $brandId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Brand';

        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brand
            ->setOptionId(null)
            ->setRowId(null)
            ->setBrandId(null)
            ->setName($name)
            ->setStoreId($requestedStoreId);

        $this->brandValidatorMock->method('validate')->willReturn(null);

        $attributeMock = $this->objectManager->getObject(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->brandAttributeRetrieverMock->method('getAttribute')->willReturn($attributeMock);

        /** @var \Magento\Eav\Model\Entity\Attribute\Option $option */
        $option = $this->objectManager->getObject(\Magento\Eav\Model\Entity\Attribute\Option::class);
        $option->setIsDefault(0)->setSortOrder(0)->setValue(0);
        $this->attributeOptionHandlerMock->method('createAttributeOption')->willReturn($option);

        $this->attributeOptionHandlerMock->method('isAdminLabelUnique')->willReturn(false);

        $this->attributeOptionHandlerMock->expects($this->never())->method('saveAttributeOption');

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('Brand with this name already exists.');

        $this->repository->save($brand);
    }

    public function testSaveExistingValidBrand()
    {
        $brandId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Brand';

        $option = $this->objectManager->getObject(\Magento\Eav\Model\Entity\Attribute\Option::class);
        $option->setIsDefault(0)->setSortOrder(0)->setValue(0);
        $option->setValue($optionId);

        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brand
            ->setOptionId($optionId)
            ->setRowId(null)
            ->setBrandId($brandId)
            ->setName($name)
            ->setStoreId($requestedStoreId)
            ->setAttributeOption($option);

        $attributeMock = $this->objectManager->getObject(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->brandAttributeRetrieverMock->method('getAttribute')->willReturn($attributeMock);

        $this->attributeOptionHandlerMock->method('isAttributeOptionIdExist')->willReturn(true);

        $this->brandValidatorMock->method('validate')->willReturn(null);

        /** @var \Magento\Eav\Model\Entity\Attribute\Option $option */

        $this->attributeOptionHandlerMock->method('isAdminLabelUnique')->willReturn(true);

        $this->attributeOptionHandlerMock->method('saveAttributeOption')->willReturn($option);
        $option->setValue($optionId);

        $this->brandResourceMock->expects($this->once())->method('save')->willReturnSelf();
        $brand->setRowId($rowId);

        $this->repository->save($brand);

        $this->assertEquals($optionId, $brand->getOptionId());
        $this->assertEquals($brandId, $brand->getBrandId());
        $this->assertEquals('test-brand', $brand->getUrlKey());
    }

    public function testSaveExistingBrandWithExistingOptionName() {
        $brandId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Brand';

        $option = $this->objectManager->getObject(\Magento\Eav\Model\Entity\Attribute\Option::class);
        $option->setIsDefault(0)->setSortOrder(0)->setValue(0);
        $option->setValue($optionId);

        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brand
            ->setOptionId($optionId)
            ->setRowId(null)
            ->setBrandId($brandId)
            ->setName($name)
            ->setStoreId($requestedStoreId)
            ->setAttributeOption($option);

        $attributeMock = $this->objectManager->getObject(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->brandAttributeRetrieverMock->method('getAttribute')->willReturn($attributeMock);

        $this->attributeOptionHandlerMock->method('isAttributeOptionIdExist')->willReturn(true);

        $this->brandValidatorMock->method('validate')->willReturn(null);

        /** @var \Magento\Eav\Model\Entity\Attribute\Option $option */

        $this->attributeOptionHandlerMock->method('isAdminLabelUnique')->willReturn(false);

        $this->attributeOptionHandlerMock->expects($this->never())->method('saveAttributeOption')->willReturn($option);

        $this->brandResourceMock->expects($this->never())->method('save')->willReturnSelf();

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('Brand with this name already exists.');

        $this->repository->save($brand);
    }

    public function testSaveExistingBrandWithMissingOption()
    {
        $brandId = 5;
        $optionId = 5;
        $requestedStoreId = 1;
        $rowId = 10;
        $name = 'Test Brand';

        $option = $this->objectManager->getObject(\Magento\Eav\Model\Entity\Attribute\Option::class);
        $option->setIsDefault(0)->setSortOrder(0)->setValue(0);
        $option->setValue($optionId);

        /** @var \Marketplacer\Brand\Model\Brand $brand */
        $brand = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand::class);
        $brand
            ->setOptionId($optionId)
            ->setRowId(null)
            ->setBrandId($brandId)
            ->setName($name)
            ->setStoreId($requestedStoreId)
            ->setAttributeOption($option);

        $attributeMock = $this->objectManager->getObject(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->brandAttributeRetrieverMock->method('getAttribute')->willReturn($attributeMock);

        $this->attributeOptionHandlerMock->method('isAttributeOptionIdExist')->willReturn(false);

        $this->attributeOptionHandlerMock->expects($this->never())->method('saveAttributeOption')->willReturn($option);

        $this->brandResourceMock->expects($this->never())->method('save')->willReturnSelf();

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('Brand attribute option with id = 5 not found.');

        $this->repository->save($brand);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testDeleteExistingById()
    {
        $brandId = 5;
        $optionId = 5;
        $requestedStoreId = Store::DEFAULT_STORE_ID;
        $rowId = 5;

        $this->prepareMocksToTestGetById($requestedStoreId);

        $this->brandMock->method('getStoreId')->willReturn($requestedStoreId);
        $this->brandMock->method('getRowId')->willReturn($rowId);
        $this->brandMock->method('getOptionId')->willReturn($optionId);
        $this->collectionMock->method('getFirstItem')->willReturn($this->brandMock);

        $attributeMock = $this->objectManager->getObject(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->brandAttributeRetrieverMock
            ->expects($this->once())
            ->method('getAttribute')
            ->willReturn($attributeMock);

        $this->attributeOptionHandlerMock
            ->expects($this->once())
            ->method('getAttributeOptionById')
            ->willReturn($this->objectManager->getObject(\Magento\Eav\Model\Entity\Attribute\Option::class));
        $this->attributeOptionHandlerMock
            ->expects($this->once())
            ->method('deleteOptionById');

        $this->brandResourceMock
            ->expects($this->once())
            ->method('delete');

        $this->assertTrue($this->repository->deleteById($brandId));
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testDeleteMissingById()
    {
        $brandId = 5;
        $requestedStoreId = Store::DEFAULT_STORE_ID;

        $this->prepareMocksToTestGetById($requestedStoreId);

        $this->brandMock->method('getRowId')->willReturn(null);
        $this->collectionMock->method('getFirstItem')->willReturn($this->brandMock);

        $attributeMock = $this->objectManager->getObject(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->brandAttributeRetrieverMock
            ->expects($this->never())
            ->method('getAttribute')
            ->willReturn($attributeMock);

        $this->attributeOptionHandlerMock
            ->expects($this->never())
            ->method('isAttributeOptionIdExist');
        $this->attributeOptionHandlerMock
            ->expects($this->never())
            ->method('deleteOptionById');

        $this->brandResourceMock
            ->expects($this->never())
            ->method('delete');

        $this->expectException(NoSuchEntityException::class);

        $this->repository->deleteById($brandId);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testIsBrandExistingOptionExist()
    {
        $brandId = 5;

        $attributeMock = $this->objectManager->getObject(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->brandAttributeRetrieverMock
            ->expects($this->once())
            ->method('getAttribute')
            ->willReturn($attributeMock);

        $this->attributeOptionHandlerMock
            ->expects($this->once())
            ->method('isAttributeOptionIdExist')
            ->willReturn(true);

        $this->assertTrue($this->repository->isBrandOptionExist($brandId));
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testIsBrandMissingOptionExist()
    {
        $brandId = 5;

        $attributeMock = $this->objectManager->getObject(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->brandAttributeRetrieverMock
            ->expects($this->once())
            ->method('getAttribute')
            ->willReturn($attributeMock);

        $this->attributeOptionHandlerMock
            ->expects($this->once())
            ->method('isAttributeOptionIdExist')
            ->willReturn(false);

        $this->assertFalse($this->repository->isBrandOptionExist($brandId));
    }

}
