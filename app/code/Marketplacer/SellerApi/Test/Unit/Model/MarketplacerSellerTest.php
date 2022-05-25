<?php

namespace Marketplacer\SellerApi\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Marketplacer\Seller\Model\Seller;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MarketplacerSellerTest extends TestCase
{
    /**
     * @var Seller
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

        $this->model = $this->objectManager->getObject(Seller::class);
    }

    public function testGetSellerId()
    {
        $value = '5';
        $this->model->setSellerId($value);
        $this->assertEquals($value, $this->model->getSellerId());
    }

    public function testGetName()
    {
        $value = 'Test Seller';
        $this->model->setName($value);
        $this->assertEquals($value, $this->model->getName());
    }


    public function testGetLogo()
    {
        $value = 'https://Test.logo/url/';
        $this->model->setLogo($value);
        $this->assertEquals($value, $this->model->getLogo());
    }

    public function testGetStoreImage()
    {
        $value = 'https://Test.store/image/url/';
        $this->model->setStoreImage($value);
        $this->assertEquals($value, $this->model->getStoreImage());
    }

    public function testGetEmailAddress()
    {
        $value = 'test@email.address';
        $this->model->setEmailAddress($value);
        $this->assertEquals($value, $this->model->getEmailAddress());
    }

    public function testGetPhone()
    {
        $value = '+1234566789';
        $this->model->setPhone($value);
        $this->assertEquals($value, $this->model->getPhone());
    }

    public function testGetAddress()
    {
        $value = 'Test address';
        $this->model->setAddress($value);
        $this->assertEquals($value, $this->model->getAddress());
    }

    public function testGetDescription()
    {
        $value = 'Test description';
        $this->model->setDescription($value);
        $this->assertEquals($value, $this->model->getDescription());
    }

    public function testOpeningHours()
    {
        $value = 'Test opening hours: 08:00-19:00';
        $this->model->setOpeningHours($value);
        $this->assertEquals($value, $this->model->getOpeningHours($value));
    }

    public function testGetBusinessNumber()
    {
        $value = '123456';
        $this->model->setBusinessNumber($value);
        $this->assertEquals($value, $this->model->getBusinessNumber());
    }

    public function testGetPolicies()
    {
        $value = 'https://test.policies/address';
        $this->model->setPolicies($value);
        $this->assertEquals($value, $this->model->getPolicies());
    }

    public function testGetShippingPolicy()
    {
        $value = 'Test shipping policy';
        $this->model->setShippingPolicy($value);
        $this->assertEquals($value, $this->model->getShippingPolicy());
    }
}
