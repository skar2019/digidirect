<?php
namespace Ewave\Navigation\Test\Unit\Ui\DataProvider\Menu\Form\Modifier;

use Ewave\Navigation\Model\Registry\Constants;
use Ewave\Navigation\Test\Unit\NavigationTestUnitTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Ewave\Navigation\Ui\DataProvider\Menu\Form\Modifier\System;

/**
 * Class SystemTest
 * @package Ewave\Navigation\Test\Unit\Ui\DataProvider\Menu\Form\Modifier
 */
class SystemTest extends \PHPUnit_Framework_TestCase
{
    use NavigationTestUnitTrait;

    protected $_objectManager;

    protected $_systemModifierTest;

    protected $_urlBuilderMock;

    protected $_registryMock;

    protected $_menuItemMock;

    public function setUp()
    {
        $this->_objectManager = new ObjectManager($this);

        $this->_urlBuilderMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\Url',
            [
                'getUrl'
            ]
        );

        $this->_registryMock = $this->getMockObjectWithoutConstructor(
            'Magento\Framework\Registry',
            ['registry']
        );

        $this->_systemModifierTest = $this->_objectManager->getObject(
            System::class,
            [
                'urlBuilder' => $this->_urlBuilderMock,
                'registry' => $this->_registryMock
            ]
        );

        $this->_menuItemMock = $this->getMockObjectWithoutConstructor(
            'Ewave\Navigation\Model\Menu',
            ['getId', 'getDataDefault', 'getData', 'getCurrentStoreId']
        );
    }

    /**
     * @dataProvider provideData
     * @param $data
     * @param $expected
     */
    public function testModifyData($data, $expected)
    {
        $this->_registryMock->expects($this->at(0))
            ->method('registry')
            ->with(Constants::CURRENT_MENU_ITEM)
            ->willReturn($this->_menuItemMock);

        $this->_menuItemMock->expects($this->any())
            ->method('getId')
            ->willReturn(11);

        $this->_menuItemMock->expects($this->any())
            ->method('getCurrentStoreId')
            ->willReturn(1);

        $this->_urlBuilderMock->expects($this->at(0))
            ->method('getUrl')
            ->with('ewave_navigation/menu/save', ['store' => 1, 'id' => 11])
            ->willReturn('some/save/url/store/1/id/11');

        $this->_urlBuilderMock->expects($this->at(1))
            ->method('getUrl')
            ->with('ewave_navigation/menu/validate', ['store' => 1, 'id' => 11])
            ->willReturn('some/validate/url/store/1/id/11');

        $result = $this->_systemModifierTest->modifyData($data);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function provideData()
    {
        return [
            [
                [
                    'config' => [
                        'submit_url' => 'before',
                        'validate_url' => 'before',
                    ]
                ],
                [
                    'config' => [
                        'submit_url' => 'some/save/url/store/1/id/11',
                        'validate_url' => 'some/validate/url/store/1/id/11',
                    ]
                ]
            ],
            [
                [
                    'config' => [
                        'back' => 'back',
                        'submit_url' => 'before',
                        'validate_url' => 'before',
                    ]
                ],
                [
                    'config' => [
                        'back' => 'back',
                        'submit_url' => 'some/save/url/store/1/id/11',
                        'validate_url' => 'some/validate/url/store/1/id/11',
                    ]
                ]
            ]
        ];
    }
}
