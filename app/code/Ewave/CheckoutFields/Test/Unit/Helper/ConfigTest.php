<?php
namespace Ewave\CheckoutFields\Test\Unit\Helper;

use Ewave\CheckoutFields\Helper\Config;

/**
 * Class ConfigTest
 * @package Ewave\CheckoutFields\Test\Unit\Helper
 */
class ConfigTest extends \Ewave\CheckoutFields\Test\Unit\TestAbstract
{
    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\App\Config
     */
    protected $_scopeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Unserialize\Unserialize
     */
    protected $_unserializer;

    /**
     * Setup objects
     */
    public function setUp()
    {
        parent::setUp();

        $this->_scopeConfigMock = $this->_getScopeConfigMock(['getValue']);

        $this->_unserializer = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\Unserialize\Unserialize',
            ['unserialize']
        );

        $this->_configHelper = $this->_objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->_scopeConfigMock,
                'unserialize' => $this->_unserializer
            ]
        );
    }

    /**
     * Get only active fields
     *
     * @return void
     */
    public function testGetActiveFields()
    {
        $fieldsConfig = [
            'delivery_number' => [
                'active' => true
            ],
            'delivery_text' => [
                'active' => false
            ]
        ];

        $this->_scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with(Config::CHECKOUT_FIELDS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ->willReturn($fieldsConfig);

        $this->_unserializer->expects($this->any())
            ->method('unserialize')
            ->with($fieldsConfig)
            ->willReturn($fieldsConfig);

        $this->assertEquals(
            ['delivery_number' => ['active' => true]],
            $this->_configHelper->getActiveCheckoutFields(null)
        );
    }
}
