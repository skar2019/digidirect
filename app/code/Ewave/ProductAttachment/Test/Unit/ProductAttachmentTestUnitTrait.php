<?php
namespace Ewave\ProductAttachment\Test\Unit;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ProductAttachmentTestUnitTrait
 * @package Ewave\ProductAttachment\Test\Unit
 */
trait ProductAttachmentTestUnitTrait
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;
    
    /**
     * Get mock object without constructor
     *
     * @param $className
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getMockObjectWithoutConstructor($className, $methods = [])
    {
        return $this->getMockBuilder($className)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Get registry mock
     *
     * @param [] $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRegistryMockWithoutConstructor($methods = ['registry'])
    {
        return $this->getMockObjectWithoutConstructor('Magento\Framework\Registry', $methods);
    }
}
