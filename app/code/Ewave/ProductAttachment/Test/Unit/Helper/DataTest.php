<?php
namespace Ewave\ProductAttachment\Test\Unit\Helper;

use Ewave\ProductAttachment\Test\Unit\ProductAttachmentTestUnitTrait;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Data\Collection;

/**
 * Class DataTest
 * @package Ewave\ProductAttachment\Test\Unit\Helper
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    use ProductAttachmentTestUnitTrait;

    /**
     * @var \Ewave\ProductAttachment\Helper\Data
     */
    protected $helper;

    /**
     * Setup objects
     *
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->contextMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\App\Helper\Context',
            ['getRequest']
        );

        $this->helper = $this->objectManager->getObject(
            'Ewave\ProductAttachment\Helper\Data',
            ['context' => $this->contextMock]
        );
    }

    /**
     *
     */
    public function testFormatFileExt()
    {
        $ext = $this->helper->formatFileExt('pdf');
        $this->assertEquals($ext, 'PDF');
    }

    /**
     * @dataProvider provideDataToByteString
     * @param $data
     * @param $expected
     */
    public function testToByteString($data, $expected)
    {
        $result = $this->helper->toByteString($data['byte']);
        $this->assertEquals($expected['byte'], $result);

        $result = $this->helper->toByteString($data['mb']);
        $this->assertEquals($expected['mb'], $result);

        $result = $this->helper->toByteString($data['kb']);
        $this->assertEquals($expected['kb'], $result);
    }

    /**
     * Provide data
     *
     * @return array
     */
    public function provideDataToByteString()
    {
        return [
            [
                ['byte' => '173', 'mb' => '1048576', 'kb' => '10240'],
                ['byte' => '173B', 'mb' => '1MB', 'kb' => '10kB']
            ]
        ];
    }
}
