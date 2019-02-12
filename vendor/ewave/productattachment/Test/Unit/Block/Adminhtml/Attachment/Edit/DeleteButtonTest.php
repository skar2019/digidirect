<?php
namespace Ewave\ProductAttachment\Test\Unit\Block\Adminhtml\Attachment\Edit;

use Ewave\ProductAttachment\Test\Unit\ProductAttachmentTestUnitTrait;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class DeleteButtonTest
 * @package Ewave\ProductAttachment\Test\Unit\Block\Adminhtml\Attachment\Edit
 */
class DeleteButtonTest extends \PHPUnit_Framework_TestCase
{
    use ProductAttachmentTestUnitTrait;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * Setup objects
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->registryMock = $this->getRegistryMockWithoutConstructor(['registry']);
    }

    /**
     * Test message before deleting
     *
     * @dataProvider provideDataDeleteButton
     * @param $data
     * @param string $expected
     */
    public function testGetButtonData($data, $expected)
    {
        $contextMock = $this->getMockObjectWithoutConstructor(
            'Magento\Backend\Block\Widget\Context',
            ['getRequest']
        );
        $this->registryMock->method('registry')->willReturn($data);
        $deleteButton = $this->objectManager->getObject(
            'Ewave\ProductAttachment\Block\Adminhtml\Attachment\Edit\DeleteButton',
            ['contextMock' => $contextMock, 'registry' => $this->registryMock]
        );
        $result = $deleteButton->getButtonData();
        $this->assertEquals($expected, $result['on_click']);
    }

    /**
     * Provide processed menu items
     *
     * @return array
     */
    public function provideDataDeleteButton()
    {
        $item = $this->getMockObjectWithoutConstructor('Ewave\ProductAttachment\Model\Attachment', ['getId']);
        $item->method('getId')->willReturn(1);
        return [
            [
                $item,
                'deleteConfirm(\'Are you sure you want to do this?\', \'\')'
            ]
        ];
    }
}
