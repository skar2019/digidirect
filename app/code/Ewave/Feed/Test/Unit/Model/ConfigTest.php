<?php

namespace Ewave\Feed\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Ewave\Feed\Model\Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * FileSystem Mock
     *
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * Model
     *
     * @var \Ewave\Feed\Model\Config
     */
    protected $model;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->filesystemMock = $this->getMockBuilder('Magento\Framework\Filesystem')->disableOriginalConstructor()
            ->getMock();

        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryRead')
            ->will($this->returnCallback(function ($code) {
                $dir = $this->getMockForAbstractClass('\Magento\Framework\Filesystem\Directory\ReadInterface');
                $dir->expects($this->any())
                    ->method('getAbsolutePath')
                    ->will($this->returnCallback(function ($path) use ($code) {
                        $path = empty($path) ? $path : '/' . $path;
                        return rtrim($code, '/') . $path . '/';
                    }));
                return $dir;
            }));

        $this->model = $this->objectManager->getObject(
            '\Ewave\Feed\Model\Config',
            [
                'filesystem' => $this->filesystemMock,
            ]
        );
    }

    /**
     * Test for getBasePath
     *
     * @covers \Ewave\Feed\Model\Config::getBasePath
     * @return void
     */
    public function testGetBasePath()
    {
        $this->assertEquals('media/feed', $this->model->getBasePath());
    }

    /**
     * Test for getTmpPath
     *
     * @covers \Ewave\Feed\Model\Config::getTmpPath
     * @return void
     */
    public function testGetTmpPath()
    {
        $this->assertEquals('media/feed/tmp', $this->model->getTmpPath());
    }

    /**
     * Test for getMaxAllowedTime
     *
     * @covers \Ewave\Feed\Model\Config::getMaxAllowedTime
     * @return void
     */
    public function testGetMaxAllowedTime()
    {
        $this->assertGreaterThan(1, $this->model->getMaxAllowedTime());
    }

    /**
     * Test for getMaxAllowedMemory
     *
     * @covers \Ewave\Feed\Model\Config::getMaxAllowedMemory
     * @return void
     */
    public function testGetMaxAllowedMemory()
    {
        $this->assertGreaterThan(10 * 1024 * 1024, $this->model->getMaxAllowedMemory());
    }
}
