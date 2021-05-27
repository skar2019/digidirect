<?php
// @codingStandardsIgnoreFile
namespace Ewave\AI\Test\Unit\Model\Lib\Entity\Import\CustomerComposite;

use Ewave\Utilities\Test\Unit\Library;
use Ewave\AI\Model\Lib\Entity\Import\CustomerComposite\CustomerComposite;

/**
 * Class CustomerCompositeTest
 *
 * @package Ewave\AI\Test\Unit\Model\Lib\Entity\Import\CustomerComposite
 */
class CustomerCompositeTest extends Library
{
    /**
     * @var CustomerComposite
     */
    protected $customerComposite;

    /**
     * Setup objects
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $reflection = $this->getReflectionClass(CustomerComposite::class);
        $this->customerComposite = $reflection->newInstanceWithoutConstructor();
    }

    /**
     * @dataProvider provideData
     * @param [] $data
     * @param [] $expected
     */
    public function testGetCustomerAddresses($data, $expected)
    {
        $class = $this->getReflectionClass(CustomerComposite::class);
        $method = $this->setAccessibleProtectedMethod($class, 'getCustomersAddresses');
        $this->assertEquals($expected, $method->invokeArgs($this->customerComposite, [$data]));
    }

    /**
     * @return array
     */
    public function provideData()
    {
        return [
            [
                [

                    [
                        'addresses' => [
                            [
                                '_email' => 'some@email',
                                '_entity_id' => '1',
                                '_website' => '14',
                            ],
                            [
                                '_entity_id' => '1',
                                '_website' => '14',
                            ],
                            [
                                '_website' => '14',
                            ]
                            ,
                            [

                            ],
                        ],
                    ],

                ],
                [

                    [
                        '_email' => 'some@email',
                        '_entity_id' => '1',
                        '_website' => '14',
                    ],
                    [
                        '_email' => '',
                        '_entity_id' => '1',
                        '_website' => '14',
                    ],
                    [
                        '_email' => '',
                        '_website' => '14',
                        '_entity_id' => '',
                    ]
                    ,
                    [
                        '_email' => '',
                        '_website' => '',
                        '_entity_id' => '',
                    ],

                ],
            ],
        ];
    }
}
