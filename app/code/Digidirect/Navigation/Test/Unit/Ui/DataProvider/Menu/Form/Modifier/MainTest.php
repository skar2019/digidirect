<?php
namespace Digidirect\Navigation\Test\Unit\Ui\DataProvider\Menu\Form\Modifier;

use Digidirect\Navigation\Model\Registry\Constants;
use Digidirect\Navigation\Test\Unit\NavigationTestUnitTrait;
use Digidirect\Navigation\Ui\DataProvider\Menu\Form\Modifier\MenuModifier;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class Main extends \PHPUnit_Framework_TestCase
{
    use NavigationTestUnitTrait;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    protected $_menuModifier;

    protected $_registryMock;

    protected $_menuItemMock;

    /**
     * Setup objects
     * @return void
     */
    public function setUp()
    {
        $this->_objectManager = new ObjectManager($this);
        $this->_registryMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\Registry',
            ['registry']
        );

        $this->_menuModifier = $this->_objectManager->getObject(
            'Digidirect\Navigation\Ui\DataProvider\Menu\Form\Modifier\Main',
            [
                'registry' => $this->_registryMock,
                'data' => ['fields' => ['title', 'status', 'category_id', 'link', 'position', 'is_logged_in']]
            ]
        );

        $this->_menuItemMock = $this->getMockObjectWithoutConstructor(
            'Digidirect\Navigation\Model\Menu',
            ['getId', 'getDataDefault', 'getData']
        );
    }

    /**
     * Test modify data
     *
     * @return void
     */
    public function testModifyData()
    {
        $this->_menuItemMock->expects($this->at(0))
            ->method('getDataDefault')
            ->willReturn(
                [
                    'id' => 2,
                    'title' => 'Default Store Title',
                    'position' => 65,
                    'cms_block_id' => 56,
                    'link' => 'link'
                ]
            );

        $this->_menuItemMock->expects($this->at(1))
            ->method('getData')
            ->willReturn(
                [
                    'id' => 2,
                    'title' => null,
                    'position' => 2,
                    'custom_options' => null,
                    'status' => 1,
                    'cms_block_id' => null,
                    'category_id' => null,
                    'set_id' => '1,2'
                ]
            );

        $this->_registryMock->expects($this->at(0))
            ->method('registry')
            ->with(\Digidirect\Navigation\Model\Registry\Constants::CURRENT_MENU_ITEM)
            ->willReturn($this->_menuItemMock);

        $expected = [
            2 => [
                'title' => 'Default Store Title',
                'position' => 2,
                'is_logged_in' => null,
                'status' => 1,
                'category_id' => null,
                'link' => 'link',
                'set_id' => [1, 2],
                'menu_store_id' => []
            ]
        ];

        $data = [2 => []];
        $this->assertEquals($expected, $this->_menuModifier->modifyData($data));
    }

    /**
     * Test for default store(0)
     * Added only categories select component
     * "Use default" checkboxes are not available
     *
     * @return void
     */
    public function testModifyMetaDefaultStore()
    {
        $reflectionClass = $this->_createReflectionClass('Digidirect\Navigation\Ui\DataProvider\Menu\Form\Modifier\Main');

        $this->_registryMock->expects($this->at(0))
            ->method('registry')
            ->with(Constants::CURRENT_MENU_ITEM)
            ->willReturn($this->_menuItemMock);

        $this->_menuItemMock->expects($this->any())
            ->method('getCurrentStoreId')
            ->willReturn(0);

        $method = $reflectionClass->getMethod('_getEditableInStoreFields');
        $method->setAccessible(true);
        $editableFields = $method->invokeArgs($this->_menuModifier, []);

        $this->_menuItemMock->expects($this->any())
            ->method('getId')
            ->willReturn(16);

        $meta = [];
        $result = $this->_menuModifier->modifyMeta($meta);
        foreach ($editableFields as $field) {
            $this->assertArrayNotHasKey($field, $result);
        }
    }

    /**
     *
     * Test modifying meta for store view
     * (extra keys will be added to array with "usr default" ui component)
     *
     * @return void
     */
    public function testModifyMetaStoreView()
    {
        $reflectionClass = $this->_createReflectionClass('Digidirect\Navigation\Ui\DataProvider\Menu\Form\Modifier\Main');

        $this->_registryMock->expects($this->at(0))
            ->method('registry')
            ->with(Constants::CURRENT_MENU_ITEM)
            ->willReturn($this->_menuItemMock);

        $this->_registryMock->expects($this->at(1))
            ->method('registry')
            ->with(Constants::CURRENT_STORE_ID)
            ->willReturn(1);

        $this->_menuItemMock->expects($this->any())
            ->method('getCurrentStoreId')
            ->willReturn(1);

        $this->_menuItemMock->expects($this->any())
            ->method('getData')
            ->willReturn([
                'title' => null,
                'position' => 6
            ]);

        $this->_menuItemMock->expects($this->any())
            ->method('getDataDefault')
            ->willReturn([
                'title' => 'Default tyile',
                'position' => 0
            ]);

        $this->_menuItemMock->expects($this->any())
            ->method('getId')
            ->willReturn(16);

        $method = $reflectionClass->getMethod('_getEditableInStoreFields');
        $method->setAccessible(true);
        $editableFields = $method->invokeArgs($this->_menuModifier, []);

        $meta = [];
        $result = $this->_menuModifier->modifyMeta($meta);
        foreach ($editableFields as $field) {
            $this->assertArrayHasKey(
                $field,
                $result[MenuModifier::MENU_ITEM_INFORMATION_DATASCOPE]['children']
            );
        }

        $this->assertArrayHasKey(
            'service',
            $result[MenuModifier::MENU_ITEM_INFORMATION_DATASCOPE]['children']['title']['arguments']['data']['config']
        );

        $this->assertTrue(
            $result[MenuModifier::MENU_ITEM_INFORMATION_DATASCOPE]['children']['title']
            ['arguments']['data']['config']['disabled']
        );
    }
}
