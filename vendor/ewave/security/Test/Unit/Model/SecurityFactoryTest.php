<?php
namespace Ewave\Security\Test\Unit\Model;

use \Ewave\Security\Model\SecurityFactory;

class SecurityFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var SecurityFactory
     */
    protected $_securityFactory;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('\Magento\Framework\ObjectManagerInterface');

        $this->_objectManager
            ->expects($this->any())
            ->method('get')
            ->will(
                $this->returnCallback(function ($className) {
                    return $this->getMock($className, [], [], '', false);
                })
            );

        $this->_securityFactory = new SecurityFactory($this->_objectManager);
    }

    public function testGet()
    {
        $this->assertInstanceOf(
            '\Ewave\Security\Helper\Data',
            $this->_securityFactory->get('\Ewave\Security\Helper\Data')
        );
    }

    public function tearDown()
    {
        $this->_securityFactory = null;
        $this->_objectManager = null;
    }
}
