<?php
namespace Ewave\CmsUpgrade\Test\Unit\Model;

/**
 * Class GeneratorTest
 * @package Ewave\CmsUpgrade\Test\Unit\Model
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Generator object
     * @var \Ewave\CmsUpgrade\Model\Generator | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_generator;

    /**
     * Generator Object reflection
     * @var \ReflectionClass
     */
    protected $_generatorReflector;

    /**
     * Helper object
     * @var \Ewave\CmsUpgrade\Helper\Data | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    /**
     * File System Driver object
     * @var \Magento\Framework\Filesystem\Driver\File | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemDriver;

    /**
     * Result object
     * @var \Magento\Framework\DataObject | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_result;

    /**
     * Data Version object
     * @var \Ewave\CmsUpgrade\Model\DataVersion | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataVersion;

    /**
     * Set up reused objects
     * @return void
     */
    public function setUp()
    {
        $this->_generator = $this->getMockBuilder('Ewave\CmsUpgrade\Model\Generator')
            ->disableOriginalConstructor()
            ->setMethods(['calculateNextVersion'])
            ->getMock();
        $this->_generatorReflector = new \ReflectionClass($this->_generator);

        $this->_helper = $this->getMockBuilder('Ewave\CmsUpgrade\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(['getModuleSetupDataDir', 'getModuleDir'])
            ->getMock();

        $helperProperty = $this->_generatorReflector->getProperty('_helper');
        $helperProperty->setAccessible(true);
        $helperProperty->setValue($this->_generator, $this->_helper);

        $this->_filesystemDriver = $this->getMockBuilder('Magento\Framework\Filesystem\Driver\File')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $fileSystemDriverProperty = $this->_generatorReflector->getProperty('_filesystemDriver');
        $fileSystemDriverProperty->setAccessible(true);
        $fileSystemDriverProperty->setValue($this->_generator, $this->_filesystemDriver);

        $this->_result = $this->getMockBuilder('Magento\Framework\DataObject')
            ->disableOriginalConstructor()
            ->setMethods(null)->getMock();

        $resultProperty = $this->_generatorReflector->getProperty('_result');
        $resultProperty->setAccessible(true);
        $resultProperty->setValue($this->_generator, $this->_result);

        $this->_dataVersion = $this->getMockBuilder('Ewave\CmsUpgrade\Model\DataVersion')
            ->disableOriginalConstructor()
            ->setMethods(['getVersion'])->getMock();

        $dataVersionProperty = $this->_generatorReflector->getProperty('_dataVersion');
        $dataVersionProperty->setAccessible(true);
        $dataVersionProperty->setValue($this->_generator, $this->_dataVersion);
    }

    /**
     * Test for check creation upgrade data folders
     */
    public function testCheckUpgradeDataFolder()
    {
        try {
            if ($this->_filesystemDriver->isExists($this->_getDataDirectory())) {
                $this->_filesystemDriver->deleteDirectory('files' . DIRECTORY_SEPARATOR . 'data' .DIRECTORY_SEPARATOR);
            }
            $this->_helper->expects($this->any())->method('getSetupDirConfigValue')
                ->willReturn($this->_getDataDirectory());
            $this->_helper->expects($this->any())->method('getModuleSetupDataDir')
                ->willReturn($this->_getDataDirectory());

            $method = $this->_generatorReflector->getMethod('_checkUpgradeDataFolder');
            $method->setAccessible(true);
            $method->invokeArgs($this->_generator, []);
        } catch (\InvalidArgumentException $notExpected) {
            $this->fail();
        }

        $this->assertTrue(true);
    }

    /**
     * Test for put upgrade file
     * @return void
     */
    public function testPutUpgradeFile()
    {
        $this->_helper->expects($this->any())->method('getModuleSetupDataDir')
            ->willReturn($this->_getDataDirectory());
        $method = $this->_generatorReflector->getMethod('_checkUpgradeDataFolder');
        $method->setAccessible(true);
        $method->invokeArgs($this->_generator, []);

        $this->_dataVersion->expects($this->any())
            ->method('getVersion')
            ->willReturn('0.0.1');

        $method = $this->_generatorReflector->getMethod('putUpgradeFile');
        $method->setAccessible(true);
        $this->assertTrue($method->invokeArgs($this->_generator, ['test data', '0.0.2']));
    }

    /**
     * Test for calculate next module version
     * @return void
     */
    public function testGetNextModuleVersion()
    {
        $this->_generator->expects($this->any())
            ->method('calculateNextVersion')
            ->with('0.0.1')
            ->willReturn('0.0.2');

        $this->_dataVersion->expects($this->any())
            ->method('getVersion')
            ->willReturn('0.0.1');

        $method = $this->_generatorReflector->getMethod('_getNextModuleVersion');
        $method->setAccessible(true);
        $this->assertEquals('0.0.2', $method->invokeArgs($this->_generator, []));
    }

    /**
     * @return string
     */
    protected function _getDataDirectory()
    {
        return 'files' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
    }
}
