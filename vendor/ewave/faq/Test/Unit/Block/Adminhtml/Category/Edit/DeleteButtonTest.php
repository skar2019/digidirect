<?php
namespace Ewave\Faq\Test\Unit\Block\Adminhtml\Category\Edit;

use Ewave\Faq\Test\Unit\FaqTestUnitTrait;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class DeleteButtonTest
 * @package Ewave\Navigation\Test\Unit\Block\Adminhtml\Menu\Edit
 */
class DeleteButtonTest extends \PHPUnit_Framework_TestCase
{
    use FaqTestUnitTrait;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registryMock;

    /**
     * Setup objects
     * @return void
     */
    public function setUp()
    {
        $this->_objectManager = new ObjectManager($this);
        $this->_registryMock = $this->_getRegistryMockWithoutConstructor(['registry']);
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
        $this->_registryMock->method('registry')->willReturn($data);
        $deleteButton = $this->_objectManager->getObject(
            'Ewave\Faq\Block\Adminhtml\Category\Edit\DeleteButton',
            ['contextMock' => $contextMock, 'registry' => $this->_registryMock]
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
        $item = $this->_getCategoryItemModelMockWithoutConstructor(['getId']);
        $item->method('getId')->willReturn(1);
        return [
            [
                $item,
                'deleteConfirm(\'Are you sure you want to delete this?\', \'\')'
            ]
        ];
    }
}
