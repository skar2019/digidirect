<?php

namespace Digidirect\AbstractEntity\Test\Unit\Helper;

use Digidirect\AbstractEntity\Helper\Data;

class DataTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $context = $this->createMock(\Magento\Framework\App\Helper\Context::class);
        $store = $this->createMock(\Magento\Store\Api\Data\StoreInterface::class);
        $store->method('getId')->willReturn(1);

        $storeManager = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $this->helper = new Data($context, $storeManager);
    }

    /**
     * @dataProvider dataTestGetIndexTablePostfix
     * @param string $attributeSetName
     * @param string $expectedResult
     */
    public function testGetIndexTablePostfix($attributeSetName, $expectedResult)
    {
        echo $this->helper->getIndexTablePostfix($attributeSetName);
        $this->assertEquals($expectedResult, $this->helper->getIndexTablePostfix($attributeSetName));
    }

    /**
     * @return array
     */
    public function dataTestGetIndexTablePostfix()
    {
        return [
            ['Test', 'test'],
            ['Test 1', 'test_1'],
            ['This string is longer than thirty characters.', 'this_string_is_longer_than_thi'],
            ['Test $# 1', 'test__1'],
            ['12Test', '12test'],
            ['!Test', 'test'],
        ];
    }
}
