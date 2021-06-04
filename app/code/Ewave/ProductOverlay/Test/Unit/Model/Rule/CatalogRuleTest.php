<?php
namespace Ewave\ProductOverlay\Test\Unit\Model\Rule;

use Ewave\ProductOverlay\Test\Unit;
use Ewave\ProductOverlay\Model\Overlays;
use Ewave\ProductOverlay\Model\ResourceModel\Rule\CatalogRule as CatalogRuleResource;
use Ewave\ProductOverlay\Model\Rule\CatalogRule;

/**
 * Class CatalogRuleTest
 * @package Ewave\ProductOverlay\Test\Unit\Model\Rule
 */
class CatalogRuleTest extends Unit\Library
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $overlayMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogRuleResource;

    /**
     * @var CatalogRule
     */
    protected $modelOriginal;

    /**
     * Setup objects
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->overlayMock = $this->getMockObjectWithoutConstructor(
            Overlays::class,
            ['getId', 'getCatalogPriceRulesIds', 'getProduct']
        );

        $this->catalogRuleResource = $this->getMockObjectWithoutConstructor(
            CatalogRuleResource::class,
            ['getPriceRuleProductsByRule']
        );

        $this->modelOriginal = $this->objectManager->getObject(CatalogRule::class);
    }

    /**
     * Test method
     * @param [] $data
     * @param bool $expected
     * @return void
     * @dataProvider provideRules
     */
    public function testIsApplicable($data, $expected)
    {
        $modelReflection = $this->getReflectionClass(CatalogRule::class);

        $this->overlayMock->expects($this->any())
            ->method('getCatalogPriceRulesIds')
            ->willReturn($data['rules']);

        $this->overlayMock->expects($this->any())
            ->method('getId')
            ->willReturn(11);

        $this->overlayMock->expects($this->any())
            ->method('getProduct')
            ->willReturn($data['product']);

        $this->catalogRuleResource->expects($this->any())
            ->method('getPriceRuleProductsByRule')
            ->willReturn($data['return']);

        $resourceProperty = $this->setAccessibleProperty($modelReflection, '_resource');
        $resourceProperty->setValue($this->modelOriginal, $this->catalogRuleResource);

        $this->assertEquals($expected, $this->modelOriginal->isApplicable($this->overlayMock));
    }

    /**
     * @return array
     */
    public function provideRules()
    {
        $productMock = $this->getMockObjectWithoutConstructor(
            \Magento\Catalog\Model\Product::class,
            ['getId']
        );

        $productMock->expects($this->any())
            ->method('getId')
            ->willReturn(1111);

        return [
            [['product' => $productMock, 'return' => [1111 => [25, 44]], 'rules' => 1], false],
            [['product' => $productMock, 'return' => [1111 => [0, 15]], 'rules' => '15, 46'], true],
            [['product' => $productMock, 'return' => [98 => [25, 15]], 'rules' => '15, 46'], false],
            [['product' => $productMock, 'return' => [1111 => [25, 44]], 'rules' => ''], false]
        ];
    }
}
