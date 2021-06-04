<?php
namespace Ewave\ProductAttachment\Test\Unit\Controller\Download;

use Ewave\ProductAttachment\Test\Unit\ProductAttachmentTestUnitTrait;
use Magento\Framework\DataObject;

/**
 * Class IndexTest
 * @package Ewave\ProductAttachment\Test\Unit\Controller\Download
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    use ProductAttachmentTestUnitTrait;

    /**
     * Test for expected not found exception if file cannot be downloaded
     */
    public function testExecute()
    {
        $this->storeManagerMock = $this->getMockObjectWithoutConstructor(
            'Magento\Store\Model\StoreManager',
            ['getStore']
        );

        $this->storeMock = $this->getMockObjectWithoutConstructor(
            'Magento\Store\Model\Store',
            ['getId']
        );

        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $attachmentRepositoryMock = $this->getMockObjectWithoutConstructor(
            'Ewave\ProductAttachment\Model\AttachmentRepository',
            ['getByIdWithAttributes']
        );

        $attachmentMock = $this->getMockObjectWithoutConstructor(
            'Ewave\ProductAttachment\Model\Attachment',
            ['isDownloadable']
        );
        $attachmentRepositoryMock->method('isDownloadable')->willReturn(false);
        $attachmentRepositoryMock->method('getByIdWithAttributes')->willReturn($attachmentMock);

        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $controller = $helper->getObject(
            'Ewave\ProductAttachment\Controller\Download\Index',
            [
                'attachmentRepository' => $attachmentRepositoryMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
        $this->setExpectedException('Magento\Framework\Exception\NotFoundException');
        $controller->execute();
    }
}
