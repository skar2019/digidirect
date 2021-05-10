<?php
namespace Digidirect\Faq\Test\Unit\Block;

use Digidirect\Faq\Test\Unit\FaqTestUnitTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class Overview
 * @package Digidirect\Faq\Test\Unit\Block
 */
class Overview extends \PHPUnit_Framework_TestCase
{
    use FaqTestUnitTrait;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilderMock;

    /**
     * @var \Digidirect\Faq\Block\Overview
     */
    protected $_block;

    /**
     * Setup objects
     * @return void
     */
    public function setUp()
    {
        $this->_objectManager = new ObjectManager($this);

        $this->_requestMock = $this->_getRequestMockWithoutConstructor(['isSecure']);

        $this->_contextMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\View\Element\Template\Context',
            ['getRequest', 'getUrlBuilder']
        );

        $this->_urlBuilderMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\Url',
            ['getUrl']
        );
        $this->_contextMock->method('getRequest')->willReturn($this->_requestMock);
        $this->_contextMock->method('getUrlBuilder')->willReturn($this->_urlBuilderMock);
        $this->_block = $this->_objectManager->getObject(
            'Digidirect\Faq\Block\Overview',
            ['context' => $this->_contextMock]
        );
    }

    /**
     *
     */
    public function testGetFaqAjaxUrl()
    {
        $this->_urlBuilderMock->method('getUrl')->willReturn('faq/index/ajaxview');
        $this->_requestMock->method('isSecure')->willReturn($this->returnValue(true));
        $this->assertEquals('faq/index/ajaxview', $this->_block->getFaqAjaxUrl());
    }
}
