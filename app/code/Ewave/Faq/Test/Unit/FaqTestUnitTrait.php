<?php
namespace Ewave\Faq\Test\Unit;

/**
 * Class NavigationTestUnitTrait
 * @package Ewave\Faq\Test\Unit
 */
trait FaqTestUnitTrait
{
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
     * Get mock object with constructor
     *
     * @param $className
     * @param array $methods
     * @param array $constructorArgs
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getMockObjectWithConstructor($className, $methods = [], $constructorArgs = [])
    {
        return $this->getMockBuilder($className)
            ->setMethods($methods)
            ->setConstructorArgs($constructorArgs)
            ->getMock();
    }

    /**
     * Get original object without constructor
     *
     * @param $className
     * @return object
     */
    public function getOriginalClassWithoutConstructor($className)
    {
        return $this->_createReflectionClass($className)->newInstanceWithoutConstructor();
    }

    /**
     * Create reflection Class
     *
     * @param $className
     * @return \ReflectionClass
     */
    protected function _createReflectionClass($className)
    {
        return new \ReflectionClass($className);
    }

    /**
     * Set accessible protected/private property
     *
     * @param \ReflectionClass $class
     * @param $propertyName
     * @return \ReflectionProperty
     */
    protected function setAccessibleProperty($class, $propertyName)
    {
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        return $property;
    }

    /**
     * Get registry mock
     *
     * @param [] $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getRegistryMockWithoutConstructor($methods = ['registry'])
    {
        return $this->getMockObjectWithoutConstructor('Magento\Framework\Registry', $methods);
    }

    /**
     * Get category model mock as used very often
     *
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCategoryItemModelMockWithoutConstructor($methods = [])
    {
        return $this->getMockObjectWithoutConstructor('Ewave\Faq\Model\Category', $methods);
    }

    /**
     * Get request object mock
     *
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getRequestMockWithoutConstructor($methods = [])
    {
        return $this->getMockObjectWithoutConstructor('Magento\Framework\App\Request\Http', $methods);
    }
}
