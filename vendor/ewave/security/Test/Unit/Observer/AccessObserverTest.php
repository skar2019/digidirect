<?php
namespace Ewave\Security\Test\Unit\Observer;

use \Magento\Framework\App\Action\Action;

class AccessObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Ewave\Security\Model\SecurityFactory
     */
    protected $_securityFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManagerMock;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlagMock;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $_observerMock;

    /**
     * @var \Ewave\Security\Observer\AccessObserver
     */
    protected $_accessObserver;

    /**
     * @var \Magento\Framework\Event
     */
    protected $_eventMock;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_httpMock;

    public function setUp()
    {
        $this->_securityFactory = $this->getMockBuilder('\Ewave\Security\Model\SecurityFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->_actionFlagMock = $this->getMockBuilder('Magento\Framework\App\ActionFlag')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_observerMock = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_eventMock = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->_httpMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods(['getClientIp'])
            ->getMock();
        
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_accessObserver = $objectManager->getObject(
            'Ewave\Security\Observer\AccessObserver',
            [
                'securityFactory' => $this->_securityFactory,
                'storeManager' => $this->_storeManagerMock,
                'actionFlag' => $this->_actionFlagMock
            ]
        );
    }

    /**
     * Extension is enabled and Ip in the range
     */
    public function testExecuteIpIsTrue()
    {
        $modelMock = $this->getMockBuilder('Ewave\Security\Model\Security')
            ->disableOriginalConstructor()
            ->getMock();
        $modelMock->expects($this->once())
            ->method('checkRangeIp')
            ->will($this->returnValue(true));
        $this->_securityFactory->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValue($modelMock)
            );

        $this->_httpMock->expects($this->any())
            ->method('getClientIp')
            ->will(
                $this->returnValue('102.168.1.1')
            );
        $this->_eventMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->_httpMock);
        $this->_observerMock->expects($this->any())
            ->method('getEvent')
            ->willReturn($this->_eventMock);

        $this->assertInstanceOf(
            'Ewave\Security\Observer\AccessObserver',
            $this->_accessObserver->execute($this->_observerMock)
        );
    }

    /**
     * Extension is enabled and Ip in not the range
     */
    public function testExecuteIpIsFalse()
    {
        $modelMock = $this->getMockBuilder('Ewave\Security\Model\Security')
            ->disableOriginalConstructor()
            ->getMock();
        $modelMock->expects($this->once())
            ->method('checkRangeIp')
            ->will($this->returnValue(false));

        $this->_securityFactory->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValue($modelMock)
            );

        $this->_httpMock->expects($this->any())
            ->method('getClientIp')
            ->will(
                $this->returnValue('102.168.1.1')
            );

        $eventMock = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getControllerAction', 'getRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $controllerActionMock = $this->getMockBuilder('Magento\Framework\App\Action\Action')
            ->setMethods(['getResponse'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        
        $responseMock = $this->getMockBuilder('Magento\Framework\App\ResponseInterface')
            ->setMethods(['setRedirect', 'sendResponse'])
            ->disableOriginalConstructor()
            ->getMock();
        
        $store = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $this->_storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($store));
        
        $responseMock->expects($this->once())->method('setRedirect')->will($this->returnValue('noroute'));

        $controllerActionMock->expects($this->any())
            ->method('getResponse')
            ->willReturn($responseMock);

        $eventMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->_httpMock);

        $eventMock->expects($this->any())
            ->method('getControllerAction')
            ->willReturn($controllerActionMock);

        $this->_observerMock->expects($this->any())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->_accessObserver->execute($this->_observerMock);
    }
    
    public function tearDown()
    {
        $this->_httpMock = null;
        $this->_storeManagerMock = null;
        $this->_actionFlagMock = null;
        $this->_securityFactory = null;
        $this->_observerMock = null;
        $this->_eventMock = null;
        $this->_accessObserver = null;
    }
}
