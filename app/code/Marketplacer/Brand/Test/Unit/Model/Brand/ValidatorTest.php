<?php

namespace Marketplacer\Brand\Test\Unit\Model\Brand;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\State;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ActionValidator\RemoveAction;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Marketplacer\Base\Model\Attribute\AttributeOptionHandler;
use Marketplacer\Brand\Model\Brand;
use Marketplacer\Brand\Model\Brand\Validator;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Model\UrlProcessor\BrandProcessorFactory;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class ValidatorTest
 * @package Marketplacer\Brand\Test\Unit\Model\Brand
 */
class ValidatorTest extends TestCase
{
    /**
     * @var RemoveAction|mixed|MockObject
     */
    private $actionValidatorMock;

    /**
     * @var Brand
     */
    private $model;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var BrandAttributeRetrieverInterface|MockObject
     */
    private $brandAttributeRetrieverMock;

    /**
     * @var AttributeOptionHandler|MockObject
     */
    private $attributeOptionHandlerMock;

    /**
     * @var BrandProcessorFactory|MockObject
     */
    private $urlProcessorFactoryMock;

    /**
     * @var AbstractResource|MockObject
     */
    private $resourceMock;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb |MockObject
     */
    private $resourceCollectionMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /** @var Validator | MockObject */
    private $validateObject;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->actionValidatorMock = $this->createMock(RemoveAction::class);
        $this->contextMock = new Context(
            $this->getMockForAbstractClass(LoggerInterface::class),
            $this->getMockForAbstractClass(ManagerInterface::class),
            $this->getMockForAbstractClass(CacheInterface::class),
            $this->createMock(State::class),
            $this->actionValidatorMock
        );
        $this->registryMock = $this->createMock(Registry::class);
        $this->resourceMock = $this->createPartialMock(AbstractDb::class, [
            '_construct',
            'getConnection',
            '__wakeup',
            'commit',
            'delete',
            'getIdFieldName',
            'rollBack'
        ]);
        $this->resourceCollectionMock = $this->getMockBuilder(\Magento\Framework\Data\Collection\AbstractDb::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->brandAttributeRetrieverMock = $this->getMockBuilder(BrandAttributeRetrieverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeOptionHandlerMock = $this->getMockBuilder(AttributeOptionHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttributeOptionById'])
            ->getMockForAbstractClass();
        $this->urlProcessorFactoryMock = $this->getMockBuilder(BrandProcessorFactory::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->objectManager = new ObjectManager($this);

        $this->model = $this->objectManager->getObject(
            Brand::class,
            [
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'brandAttributeRetriever' => $this->brandAttributeRetrieverMock,
                'attributeOptionHandler' => $this->attributeOptionHandlerMock,
                'urlProcessorFactory' => $this->urlProcessorFactoryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
        $this->validateObject = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['validate'])
            ->getMock();
    }

    public function testValidateException()
    {
        $this->validateObject->expects($this->never())->method('validate')->with([
            $this->model, false
        ]);
        $this->expectException(LocalizedException::class);
        $this->objectManager->getObject(Validator::class)->validate($this->model, false);
    }

    public function testValidate()
    {
        $this->validateObject->expects($this->never())->method('validate')->with([
            $this->model, false
        ]);

        $this->model->setData([
            BrandInterface::NAME => 'Name',
        ]);

        $validator = $this->objectManager->getObject(Validator::class);
        $validator->validate($this->model, false);
        $this->assertEquals('Name', $this->model->getName());
    }

    public function testSkipValidationFlagValidate()
    {
        $this->validateObject->expects($this->never())->method('validate')->with([
            $this->model, false
        ]);

        $this->model->setData([
            '_skip_validation_flag' => true,
            BrandInterface::NAME => 'Name',
        ]);

        $validator = $this->objectManager->getObject(Validator::class);
        $validator->validate($this->model, false);
        $this->assertEquals('Name', $this->model->getName());
    }
}
