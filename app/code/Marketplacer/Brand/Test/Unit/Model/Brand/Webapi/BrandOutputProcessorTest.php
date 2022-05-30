<?php

namespace Marketplacer\Brand\Test\Unit\Model\Brand\Webapi;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Model\Brand;
use Marketplacer\Brand\Model\Brand\Webapi\BrandOutputProcessor;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class BrandOutputProcessorTest
 * @package Marketplacer\Brand\Test\Unit\Model\Brand\Webapi
 */
class BrandOutputProcessorTest extends TestCase
{
    /**
     * @var Brand
     */
    private $brandObject;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var BrandOutputProcessor
     */
    private $brandOutputProcessor;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->brandOutputProcessor = $this->objectManager->getObject(BrandOutputProcessor::class);
    }

    public function testExecute() {
        $result = [
            'id'=> 10,
            'logo' => 'logo',
            'name' => 'Name',
        ];
        $this->brandObject = $this->objectManager->getObject(Brand::class);
        $this->brandObject->setData([
            BrandInterface::NAME => 'Name',
            MarketplacerBrandInterface::BRAND_ID => 10,
            MarketplacerBrandInterface::LOGO => 'logo',

        ]);
        $resultfromfun =  $this->brandOutputProcessor->execute($this->brandObject, $result);
        $this->assertEquals($result, $resultfromfun);
    }

    public function testExecuteWithExtraBrandData() {
        $result = [
            'id'=> 10,
            'logo' => 'logo',
            'name' => 'Name',
        ];
        $this->brandObject = $this->objectManager->getObject(Brand::class);
        $this->brandObject->setData([
            BrandInterface::NAME => 'Name',
            MarketplacerBrandInterface::BRAND_ID => 10,
            MarketplacerBrandInterface::LOGO => 'logo',
            'test1' => '',
            'test2' => ''
        ]);
        $resultfromfun =  $this->brandOutputProcessor->execute($this->brandObject, $result);
        $this->assertEquals($result, $resultfromfun);
    }
}
