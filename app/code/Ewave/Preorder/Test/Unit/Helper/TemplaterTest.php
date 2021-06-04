<?php
namespace Ewave\PreOrder\Test\Unit\Helper;

use Ewave\PreOrder\Test\Unit\PreOrderTestUnitTrait;
use Ewave\PreOrder\Helper\Templater;
use Magento\Catalog\Model\Product;

/**
 * Class TemplaterTest
 * @package Ewave\PreOrder\Test\Unit\Helper
 */
class TemplaterTest extends \PHPUnit_Framework_TestCase
{
    use PreOrderTestUnitTrait;

    public function testProcessExpectSameAsTemplateArgument()
    {
        $templateArgument = 'Pre Order';
        $helperMock = $this->getMockObjectWithoutConstructor(
            Templater::class,
            ['attributeReplaceCallback']
        );
        $helperMock->expects($this->any())
            ->method('attributeReplaceCallback')
            ->willReturn($templateArgument);
        $productMock = $this->getMockObjectWithoutConstructor(
            Product::class,
            null
        );

        $result = $helperMock->process($templateArgument, $productMock);

        $this->assertEquals($result, $templateArgument);
    }
}
