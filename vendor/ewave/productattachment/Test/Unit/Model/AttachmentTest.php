<?php
namespace Ewave\ProductAttachment\Test\Unit\Model;

use Ewave\ProductAttachment\Test\Unit\ProductAttachmentTestUnitTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class AttachmentTest
 * @package Ewave\ProductAttachment\Test\Unit\Model
 */
class AttachmentTest extends \PHPUnit_Framework_TestCase
{
    use ProductAttachmentTestUnitTrait;

    /**
     * @var \Ewave\ProductAttachment\Model\Attachment
     */
    protected $attachment;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileProcessorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactory;

    /**
     * Setup objects
     *
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->contextMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\Model\Context',
            []
        );

        $this->fileProcessorMock = $this->getMockObjectWithoutConstructor(
            'Ewave\ProductAttachment\Model\FileProcessor',
            ['getFileExt', 'getMediaFilePath']
        );

        $this->storeManagerMock = $this->getMockObjectWithoutConstructor(
            'Magento\Store\Model\StoreManager',
            ['getStore']
        );

        $this->storeMock = $this->getMockObjectWithoutConstructor(
            'Magento\Store\Model\Store',
            ['getId', 'isCurrentlySecure', 'getBaseUrl']
        );

        $this->collectionFactory = $this->getMockObjectWithoutConstructor(
            'Ewave\ProductAttachment\Model\ResourceModel\Attachment\CollectionFactory',
            ['create']
        );

        $this->attachment = $this->objectManager->getObject(
            'Ewave\ProductAttachment\Model\Attachment',
            [
                'context' => $this->contextMock,
                'fileProcessor' => $this->fileProcessorMock,
                'storeManager' => $this->storeManagerMock,
                'resourceCollectionFactory' => $this->collectionFactory,
            ]
        );
    }

    /**
     * Check if attachment has attribute
     */
    public function testGetAttachmentAttributes()
    {
        $attributes = $this->attachment->getAttachmentAttributes();
        $this->assertCount(1, $attributes);
    }

    /**
     * Check downloadable file name
     */
    public function testGetDownloadableFileName()
    {
        $this->fileProcessorMock->method('getFileExt')->willReturn('pdf');
        $this->attachment->setName('test');
        $result = $this->attachment->getDownloadableFileName();
        $this->assertEquals('test.pdf', $result);
    }

    /**
     * Check if attachment is downloadable
     */
    public function testIsDownloadable()
    {
        $this->attachment->setId('1');
        $this->attachment->setStatus('1');
        $this->assertTrue($this->attachment->isDownloadable());
    }

    /**
     * Check attachment web url
     */
    public function testGetWebUrl()
    {
        $this->fileProcessorMock->method('getMediaFilePath')->willReturn('1/1/test.pdf');
        $this->storeMock->method('isCurrentlySecure')->willReturn(false);
        $this->storeMock->method('getBaseUrl')->willReturn('http://lego.local/');
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $result = $this->attachment->getWebUrl();
        $this->assertEquals('http://lego.local/1/1/test.pdf', $result);
    }
}
