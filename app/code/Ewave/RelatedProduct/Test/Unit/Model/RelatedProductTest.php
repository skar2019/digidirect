<?php
namespace Ewave\RelatedProduct\Test\Unit\Model;

use Ewave\RelatedProduct\Test\Unit\RelatedProductTestUnitTrait;
use Ewave\RelatedProduct\Model\RelatedProduct;

/**
 * Class RelatedProductTest
 * @package Ewave\RelatedProduct\Test\Unit\Model
 */
class RelatedProductTest extends \PHPUnit_Framework_TestCase
{
    use RelatedProductTestUnitTrait;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelMock;

    /**
     * Setup objects
     * @return void
     */
    public function setUp()
    {
        $this->_modelMock = $this->getMockObjectWithoutConstructor(
            RelatedProduct::class,
            ['getAttributes']
        );
    }

    /**
     * @param $data
     * @param $expected
     * @dataProvider provideProductAttributes
     */
    public function testHasProductAttributeColorExpectedTrue($data, $expected)
    {
        $this->_modelMock->expects($this->once())
            ->method('getAttributes')
            ->willReturn($data);

        $result = $this->_modelMock->hasProductAttribute('color');

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function provideProductAttributes()
    {
        return [
            [['color' => ''], true],
            [[], false]
        ];
    }
}
