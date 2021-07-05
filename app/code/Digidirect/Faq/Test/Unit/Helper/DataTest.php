<?php
namespace Digidirect\Faq\Test\Unit\Helper;

use Digidirect\Faq\Test\Unit\FaqTestUnitTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class DataTest
 * @package Digidirect\Faq\Test\Unit\Helper
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    use FaqTestUnitTrait;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Digidirect\Faq\Helper\Data
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeConfigMock;

    /**
     * Setup objects
     * @return void
     */
    public function setUp()
    {
        $this->_objectManager = new ObjectManager($this);

        $this->_contextMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\App\Helper\Context',
            ['getRequest']
        );

        $this->_scopeConfigMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\App\Helper\Context',
            ['getValue']
        );

        $this->_helper = $this->_objectManager->getObject(
            'Digidirect\Faq\Helper\Data',
            ['context' => $this->_contextMock, 'scopeConfig' => $this->_scopeConfigMock]
        );
    }

    /**
     * Test ajax url
     */
    public function testGetAjaxCallUrl()
    {
        $this->assertEquals('faq/index/ajaxview', $this->_helper->getAjaxCallUrl());
    }

    /**
     * @dataProvider provideIsValidUrlData
     * @param $data
     * @param $expected
     */
    public function testIsValidUrl($data, $expected)
    {
        $this->_scopeConfigMock->method('getValue')->willReturn($expected);
        $this->assertTrue($this->_helper->isValidUrl($data));
    }

    /**
     * @return array
     */
    public function provideIsValidUrlData()
    {
        return [['faq', 'faq']];
    }
}
