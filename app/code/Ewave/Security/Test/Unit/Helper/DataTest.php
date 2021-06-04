<?php
namespace Ewave\Security\Test\Unit\Helper;

use Ewave\Security\Helper\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $_helper;

    /**
     * Scope config mock
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeConfig;

    /**
     * @var
     */
    protected $_contextMock;

    public function setUp()
    {
        $this->_scopeConfig = $this->getMockForAbstractClass(
            'Magento\Framework\App\Config\ScopeConfigInterface',
            ['getValue'],
            '',
            false
        );

        $this->_contextMock = $this->getMock(
            'Magento\Framework\App\Helper\Context',
            ['getScopeConfig'],
            [],
            '',
            false
        );

        $this->_helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
    }

    /**
     * 
     */
    public function testIsAccessEnabledIsTrue()
    {
        $this->_scopeConfig->expects($this->at(0))
            ->method('isSetFlag')
            ->with(Data::XML_PATH_SECURITY_ACCESS_ENABLED)
            ->will(
                $this->returnValue(1)
            );

        $this->_scopeConfig->expects($this->at(1))
            ->method('getValue')
            ->with(Data::XML_PATH_SECURITY_ACCESS_IPS)
            ->will(
                $this->returnValue('1.2.3.4')
            );
        $this->_contextMock->expects($this->any())
            ->method('getScopeConfig')
            ->will(
                $this->returnValue($this->_scopeConfig)
            );

        $helperData = $this->_helper->getObject('Ewave\Security\Helper\Data', [
            'context' => $this->_contextMock
        ]);

        $this->assertTrue($helperData->isAccessEnabled());
    }

    /**
     *
     */
    public function testIsAccessEnabledIsFalse()
    {
        $this->_scopeConfig->expects($this->at(0))
            ->method('isSetFlag')
            ->with(Data::XML_PATH_SECURITY_ACCESS_ENABLED)
            ->will(
                $this->returnValue(1)
            );

        $this->_scopeConfig->expects($this->at(1))
            ->method('getValue')
            ->with(Data::XML_PATH_SECURITY_ACCESS_IPS)
            ->will(
                $this->returnValue('')
            );
        $this->_contextMock->expects($this->any())
            ->method('getScopeConfig')
            ->will(
                $this->returnValue($this->_scopeConfig)
            );

        $helperData = $this->_helper->getObject('Ewave\Security\Helper\Data', [
            'context' => $this->_contextMock
        ]);

        $this->assertFalse($helperData->isAccessEnabled());
    }
}
