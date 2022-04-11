<?php

namespace Marketplacer\BrandApi\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Marketplacer\Brand\Model\Brand;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MarketplacerBrandTest extends TestCase
{
    /**
     * @var Brand
     */
    private $model;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->model = $this->objectManager->getObject(Brand::class);
    }

    public function testGetBrandId()
    {
        $value = '5';
        $this->model->setBrandId($value);
        $this->assertEquals($value, $this->model->getBrandId());
    }

    public function testGetName()
    {
        $value = 'Test Brand';
        $this->model->setName($value);
        $this->assertEquals($value, $this->model->getName());
    }

    public function testGetLogo()
    {
        $value = 'https://Test.logo/url/';
        $this->model->setLogo($value);
        $this->assertEquals($value, $this->model->getLogo());
    }
}
