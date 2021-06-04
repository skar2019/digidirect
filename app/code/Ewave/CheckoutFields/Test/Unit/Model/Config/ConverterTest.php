<?php

namespace Ewave\CheckoutFields\Test\Unit\Model\Config;

/**
 * Class ConverterTest
 *
 * @package Ewave\CheckoutFields\Test\Unit\Model\Config
 */
class ConverterTest extends \Ewave\CheckoutFields\Test\Unit\TestAbstract
{

    /**
     * @var \Ewave\CheckoutFields\Model\Config\Converter
     */
    private $model;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceConfigMock;

    /**
     * @var string
     */
    private $fixturePath;

    /**
     * Setup objects
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->resourceConfigMock = $this->getMock('Magento\Framework\App\ResourceConnection\ConfigInterface');
        $this->model = $this->_objectManager->getObject('Ewave\CheckoutFields\Model\Config\Converter');
        $this->fixturePath = realpath(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
    }

    /**
     * Test convert
     *
     * @return void
     */
    public function testConvert()
    {
        $this->resourceConfigMock->expects($this->any())
            ->method('getConnectionName')
            ->willReturnCallback(function ($resourceName) {
                return $resourceName;
            });
        $dom = new \DOMDocument();
        $dom->load($this->fixturePath . 'checkout_fields.xml');
        $constraints = $this->model->convert($dom);
        $expectedResult = [
            'checkout_fields' => [
                'fields' => [
                    'options_test' => [
                        'frontend_name' => 'Delivery Type',
                        'frontend_input' => 'select',
                        'sort_order' => '0',
                        'validation' => [
                            'rule' => [
                                0 => [
                                    '_value' => '1',
                                    '_attribute' => [
                                        'name' => 'required-entry'
                                    ],
                                ],
                                1 => [
                                    '_value' => '1',
                                    '_attribute' => [
                                        'name' => 'validate-email'
                                    ],
                                ]
                            ]
                        ],
                        'area' => [
                            'checkout_step' => 'shipping-step',
                            'custom_scope' => 'shippingAddress',
                            'fieldset' => 'address-list-additional-addresses',
                        ],
                        'options' => [
                            'option' => [
                                '0' => [
                                    '_value' => null,
                                    '_attribute' => [
                                        'value' => '1',
                                        'label' => 'Test Label 1'
                                    ]
                                ],
                                '1' => [
                                    '_value' => null,
                                    '_attribute' => [
                                        'value' => '2',
                                        'label' => 'Test Label 2'
                                    ]
                                ]
                            ]
                        ],
                    ]
                ]
            ]
        ];
        $this->assertEquals($expectedResult, $constraints);
    }
}
