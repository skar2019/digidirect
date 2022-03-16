<?php

namespace Marketplacer\Brand\Test\Unit\Model\Layer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Category\CollectionFilter;
use Magento\Catalog\Model\Layer\ContextInterface;
use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\Catalog\Model\Layer\State;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Model\Brand;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class BrandTest
 * @package Marketplacer\Brand\Test\Unit\Model\Layer
 */
class BrandTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    private $brandAttributeRetrieverMock;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var Brand
     */
    private $brandObject;

    /**
     * @var Registry|MockObject
     */
    private $registry;

    /**
     * @var Collection|MockObject
     */
    private $collection;

    /**
     * @var ItemCollectionProviderInterface|MockObject
     */
    private $collectionProvider;

    /**
     * @var ContextInterface|MockObject
     */
    private $context;

    /**
     * @var CollectionFilter|MockObject
     */
    private $collectionFilter;

    /**
     * @var Category|MockObject
     */
    private $category;

    /**
     * @var object
     */
    private $model;

    /**
     * @var string
     */
    private $attributeCode = 'marketplacer_brand';

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->brandObject = $this->objectManager->getObject(Brand::class);
        $this->brandObject->setData([
            BrandInterface::NAME      => 'Name',
            BrandInterface::BRAND_ID  => 10,
            BrandInterface::STORE_ID  => Store::DEFAULT_STORE_ID,
            BrandInterface::OPTION_ID => 5
        ]);

        $this->category = $this->getMockBuilder(Category::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry = new Registry();
        $this->registry->register('current_brand', $this->brandObject);
        $this->registry->register('current_category', $this->category);

        $this->collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionFilter = $this->getMockBuilder(CollectionFilter::class)
            ->setMethods(['filter'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionProvider = $this->getMockBuilder(ItemCollectionProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->context = $this->getMockBuilder(ContextInterface::class)
            ->setMethods(['getStateKey', 'getCollectionFilter'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->context->expects($this->any())
            ->method('getCollectionFilter')
            ->willReturn($this->collectionFilter);
        $this->context->expects($this->any())
            ->method('getCollectionProvider')
            ->willReturn($this->collectionProvider);

        $this->state = $this->getMockBuilder(State::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoryRepository = $this->getMockForAbstractClass(CategoryRepositoryInterface::class);
        $this->currentCategory = $this->createPartialMock(
            Category::class,
            ['getId']
        );
        $this->brandAttributeRetrieverMock = $this->getMockBuilder(BrandAttributeRetrieverInterface::class)
            ->setMethods(['getAttributeCode'])
            ->getMockForAbstractClass();
        $this->brandAttributeRetrieverMock->expects($this->any())->method('getAttributeCode')
            ->willReturn($this->attributeCode);

        $this->model = $this->objectManager->getObject(\Marketplacer\Brand\Model\Layer\Brand::class,
            [
                'registry'                => $this->registry,
                'context'                 => $this->context,
                'brandAttributeRetriever' => $this->brandAttributeRetrieverMock,
            ]
        );
    }

    public function testGetProductCollection()
    {
        $this->objectManager = new ObjectManager($this);

        $this->category->expects($this->any())->method('getId')->willReturn(333);
        $this->collectionFilter->expects($this->once())
            ->method('filter')
            ->with($this->collection, $this->category);
        $this->collectionProvider->expects($this->once())->method('getCollection')
            ->with($this->category)
            ->willReturn($this->collection);

        $this->collection->expects($this->once())->method('addFieldToFilter')
            ->with($this->attributeCode, 5)
            ->willReturnSelf();

        $result = $this->model->getProductCollection();
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testGetCurrentBrand()
    {
        $this->objectManager = new ObjectManager($this);
        $this->brandObject = $this->objectManager->getObject(Brand::class);
        $this->brandObject->setData(
            [
                BrandInterface::NAME      => 'Name',
                BrandInterface::BRAND_ID  => 10,
                BrandInterface::STORE_ID  => Store::DEFAULT_STORE_ID,
            ]
        );

        $brandAttributeRetrieverMock = $this->getMockBuilder(BrandAttributeRetrieverInterface::class)
            ->getMock();
        $this->registry = new Registry();
        $this->registry->register('current_brand', $this->brandObject);

        $brand = $this->objectManager->getObject(\Marketplacer\Brand\Model\Layer\Brand::class,
            [
                'registry'                  => $this->registry,
                'brandAttributeRetriever'  => $brandAttributeRetrieverMock,
            ]
        );
        $brandInfo = $brand->getCurrentBrand();
        $this->assertEquals($this->brandObject, $brandInfo);
    }
}
