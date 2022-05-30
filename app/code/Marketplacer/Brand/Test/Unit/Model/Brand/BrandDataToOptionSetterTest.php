<?php

namespace Marketplacer\Brand\Test\Unit\Model\Brand;

use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Model\Brand;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class BrandDataToOptionSetterTest
 * @package Marketplacer\Brand\Test\Unit\Model\Brand
 */
class BrandDataToOptionSetterTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var AttributeOptionLabelInterface
     */
    private $storeLabel;

    /**
     * @var AttributeOptionInterface
     */
    private $attributeOptionObject;

    /**
     * @var object
     */
    private $brandOptionSetter;

    /**
     * @var Option
     */
    private $attriOptionWithoutStoreLabel;

    /**
     * @var AttributeOptionLabelInterfaceFactory|MockObject
     */
    private $attributeOptionLabelFactoryMock;

    /**
     * @var Brand
     */
    private $brandObject;

    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->storeLabel = $this->objectManager->getObject(Option::class);
        $this->storeLabel->setLabel('test');
        $this->storeLabel->setStoreId(1);

        $this->attributeOptionObject = $this->objectManager->getObject(Option::class);
        $this->attributeOptionObject->setData([
            AttributeOptionInterface::LABEL => 'test',
            AttributeOptionInterface::VALUE => 'value',
            AttributeOptionInterface::IS_DEFAULT => 'defualt',
            AttributeOptionInterface::SORT_ORDER => '1',
            AttributeOptionLabelInterface::STORE_ID => 1,
            AttributeOptionInterface::STORE_LABELS =>  [
                $this->storeLabel,
            ],
        ]);

        $this->attributeOptionLabelFactoryMock = $this->createMock(AttributeOptionLabelInterfaceFactory::class);
        $this->attributeOptionLabelFactoryMock->method('create')->willReturn($this->objectManager->getObject(\Magento\Eav\Model\Entity\Attribute\OptionLabel::class));
        $this->brandOptionSetter = $this->objectManager->getObject(\Marketplacer\Brand\Model\Brand\BrandDataToOptionSetter::class,
            [
                'attributeOptionLabelFactory'  => $this->attributeOptionLabelFactoryMock,
            ]
        );
    }

    public function testUpdateStoreLabels ()
    {
        $this->brandObject = $this->objectManager->getObject(Brand::class);
        $this->brandObject ->setData([
            BrandInterface::NAME => 'Name',
            MarketplacerBrandInterface::BRAND_ID => 10,
            BrandInterface::STORE_ID => 1,
        ]);
        $this->brandOptionSetter->setFromBrand($this->brandObject , $this->attributeOptionObject);
        $storeLabels = $this->attributeOptionObject->getStoreLabels();
        foreach ($storeLabels as $storeLabel) {
            $this->assertEquals($this->brandObject->getName(), $storeLabel->getLabel());
        }
    }

    public function testBrandDefaultStoreId()
    {
        $this->brandObject = $this->objectManager->getObject(Brand::class);
        $this->brandObject->setData([
            BrandInterface::NAME => 'Name',
            MarketplacerBrandInterface::BRAND_ID => 10,
            BrandInterface::STORE_ID => Store::DEFAULT_STORE_ID,
        ]);
        $this->brandOptionSetter->setFromBrand($this->brandObject, $this->attributeOptionObject);
        $this->assertEquals($this->brandObject->getName(), $this->attributeOptionObject->getLabel());
    }

    public function testAttrOptionsWithoutStoreLabels()
    {
        $this->brandObject = $this->objectManager->getObject(Brand::class);
        $this->brandObject->setData([
            BrandInterface::NAME => 'Name',
            MarketplacerBrandInterface::BRAND_ID => 10,
            MarketplacerBrandInterface::LOGO => 'logo',
            BrandInterface::OPTION_ID => '',
            BrandInterface::META_TITLE => '',
            BrandInterface::META_DESCRIPTION => '',
            BrandInterface::STATUS => '',
            BrandInterface::STORE_ID => 1,
        ]);

        $this->attriOptionWithoutStoreLabel = $this->objectManager->getObject(Option::class);
        $this->attriOptionWithoutStoreLabel->setData([
            AttributeOptionInterface::LABEL => 'test',
            AttributeOptionInterface::VALUE => 'value',
            AttributeOptionInterface::IS_DEFAULT => 'defualt',
            AttributeOptionInterface::SORT_ORDER => '1',
            AttributeOptionLabelInterface::STORE_ID => 1,
        ]);
        $this->brandOptionSetter->setFromBrand($this->brandObject, $this->attriOptionWithoutStoreLabel);
        $storeLabels = $this->attriOptionWithoutStoreLabel->getStoreLabels();
        foreach ($storeLabels as $storeLabel) {
            $this->assertEquals($this->brandObject->getName(), $storeLabel->getLabel());
            $this->assertEquals($this->brandObject->getStoreId(), $storeLabel->getStoreId());
        }
    }

    public function testValidateException()
    {
        $this->brandObject = $this->objectManager->getObject(Brand::class);
        $this->brandObject->setData([
            BrandInterface::NAME => '',
            MarketplacerBrandInterface::BRAND_ID => 10,
            BrandInterface::STORE_ID => Store::DEFAULT_STORE_ID,
        ]);
        $this->expectException(LocalizedException::class);
        $this->brandOptionSetter->setFromBrand($this->brandObject, $this->attributeOptionObject);
    }
}
