<?php
namespace Digidirect\PreOrder\Test\Unit\Helper;

use Digidirect\PreOrder\Test\Unit\PreOrderTestUnitTrait;
use Digidirect\PreOrder\Helper\Templater;
use Magento\Catalog\Model\Product;

/**
 * Class TemplaterTest
 * @package Digidirect\PreOrder\Test\Unit\Helper
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
