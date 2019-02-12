<?php

namespace Ewave\Security\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

class SecurityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Ewave\Security\Helper\Data;
     */
    protected $_helperMock;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Ewave\Security\Model\Security
     */
    protected $_model;

    /**
     * Valid formats
     * @return array
     */
    public function validFormats()
    {
        return [
            ['1.2.3.4'],
            ['172.68.5.29'],
            ['192.168.1.50'],
            ['100.5.6.123'],
            ['10.20.30.40'],
            ['1.2.3.4,20.21.22.23']
        ];
    }

    /**
     * Not valid formats
     * @return array
     */
    public function notValidFormats()
    {
        return [
            ['172.2.3.4'],
            ['34.43.5.29'],
            ['87.41.1.50'],
            ['99.234.43.99'],
            ['1.1.1.1'],
            ['172.2.3.4,1.1.1.1']
        ];
    }

    public function setUp()
    {
        $this->_helperMock = $this->getMockBuilder('Ewave\Security\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_objectManager = new ObjectManager($this);
        $this->_model = $this->_objectManager->getObject(
            'Ewave\Security\Model\Security',
            [
                'helper' => $this->_helperMock
            ]
        );
    }

    /**
     * @param $expectedIp
     * @dataProvider validFormats
     */
    public function testCheckRangeIp($expectedIp)
    {
        $this->_helperMock->expects($this->at(0))
            ->method('isAccessEnabled')
            ->will(
                $this->returnValue(true)
            );

        $this->_helperMock->expects($this->any())
            ->method('getIpsToArray')
            ->will(
                $this->returnValue(
                    ['1.2.3.4', '172.68.5.*', '192.168.1.0-192.168.1.100', '100.5.6/24', '10.20.30.40/255.255.255.0']
                )
            );

        $this->assertTrue($this->_model->checkRangeIp($expectedIp));
    }

    /**
     * @param $expectedIp
     * @dataProvider notValidFormats
     */
    public function testCheckRangeIpIsNot($expectedIp)
    {
        $this->_helperMock->expects($this->at(0))
            ->method('isAccessEnabled')
            ->will(
                $this->returnValue(true)
            );

        $this->_helperMock->expects($this->any())
            ->method('getIpsToArray')
            ->will(
                $this->returnValue(
                    ['1.2.3.4', '172.68.5.*', '192.168.1.0-192.168.1.100', '100.5.6/24', '10.20.30.40/255.255.255.0']
                )
            );

        $this->assertFalse($this->_model->checkRangeIp($expectedIp));
    }

    /**
     * Checking of inputting data from backend
     * @param $expectedIp
     * @dataProvider notValidFormats
     */
    public function testCheckRangeIpValidValue($expectedIp)
    {
        $this->_helperMock->expects($this->at(0))
            ->method('isAccessEnabled')
            ->will(
                $this->returnValue(true)
            );

        $this->_helperMock->expects($this->any())
            ->method('getIpsToArray')
            ->will(
                $this->returnValue(
                    ['1.2.3.test', '172.test.5.*', 'test.5.6/24', '10.20.test.40/255.255.255.0']
                )
            );

        $this->assertFalse($this->_model->checkRangeIp($expectedIp), 'Checking of inputting data from backend');
    }

    /**
     *
     */
    public function testCheckRangeIpIsAccessEnabledIsFalse()
    {
        $this->_helperMock->expects($this->at(0))
            ->method('isAccessEnabled')
            ->will(
                $this->returnValue(false)
            );

        $this->assertTrue($this->_model->checkRangeIp('1.2.3.4'));
    }
}
