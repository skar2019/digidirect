<?php
namespace Digidirect\OutOfStockNotif\Test\Unit\Block\Product\View;

/**
 * Test class for \Digidirect\OutOfStockNotif\Block\Product\View\Stock
 */
class StockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\ProductAlert\Helper\Data
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Digidirect\OutOfStockNotif\Helper\Data
     */
    protected $_digidirectHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Digidirect\OutOfStockNotif\Block\Product\View\Stock
     */
    protected $_block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\Layout
     */
    protected $_layout;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_helper = $this->getMock(
            'Magento\ProductAlert\Helper\Data',
            ['isStockAlertAllowed', 'getSaveUrl'],
            [],
            '',
            false
        );
        $this->_digidirectHelper = $this->getMock(
            'Digidirect\OutOfStockNotif\Helper\Data',
            ['isEnabledForGuest'],
            [],
            '',
            false
        );
        $this->_product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['isAvailable', 'getId', '__wakeup', 'getTypeId'],
            [],
            '',
            false
        );
        $this->_product->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->_product->expects($this->any())->method('getTypeId')->will($this->returnValue('configurable'));
        $this->_registry = $this->getMockBuilder(
            'Magento\Framework\Registry'
        )->disableOriginalConstructor()->setMethods(
            ['registry']
        )->getMock();

        $this->_block = $objectManager->getObject(
            'Digidirect\OutOfStockNotif\Block\Product\View\Stock',
            ['helper' => $this->_helper, 'digidirectHelper' => $this->_digidirectHelper, 'registry' => $this->_registry]
        );
        $this->_layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
    }

    public function testSetTemplateStockUrlAllowed()
    {
        $this->_registry->expects(
            $this->any()
        )->method(
            'registry'
        )->with(
            'current_product'
        )->will(
            $this->returnValue($this->_product)
        );

        $this->_helper->expects($this->once())->method('isStockAlertAllowed')->will($this->returnValue(true));
        $this->_helper->expects(
            $this->once()
        )->method(
            'getSaveUrl'
        )->with(
            'stock'
        )->will(
            $this->returnValue('http://url')
        );

        $this->_product->expects($this->once())->method('isAvailable')->will($this->returnValue(false));

        $this->_block->setLayout($this->_layout);
        $this->_block->setDefaultTemplate('path/to/default/template.phtml');
        $this->_block->setGuestTemplate('path/to/template.phtml');
        $this->_block->setTemplate('path/to/template.phtml');

        $this->assertEquals('path/to/template.phtml', $this->_block->getTemplate());
        $this->assertEquals('http://url', $this->_block->getSignupUrl());
    }

    /**
     * @param bool $stockAlertAllowed
     * @param bool $productAvailable
     * @dataProvider setTemplateStockUrlNotAllowedDataProvider
     */
    public function testSetTemplateStockUrlNotAllowed($stockAlertAllowed, $productAvailable)
    {
        $this->_helper->expects(
            $this->once()
        )->method(
            'isStockAlertAllowed'
        )->will(
            $this->returnValue($stockAlertAllowed)
        );
        $this->_helper->expects($this->never())->method('getSaveUrl');

        $this->_product->expects($this->any())->method('isAvailable')->will($this->returnValue($productAvailable));

        $this->_registry->expects(
            $this->any()
        )->method(
            'registry'
        )->with(
            'current_product'
        )->will(
            $this->returnValue($this->_product)
        );

        $this->_block->setLayout($this->_layout);
        $this->_block->setTemplate('path/to/template.phtml');

        $this->assertEquals('', $this->_block->getTemplate());
        $this->assertNull($this->_block->getSignupUrl());
    }

    public function setTemplateStockUrlNotAllowedDataProvider()
    {
        return [
            'stock alert not allowed' => [false, false],
            'product is available (no alert)' => [true, true],
            'stock alert not allowed and product is available' => [false, true]
        ];
    }

    public function testSetTemplateNoProduct()
    {
        $this->_helper->expects($this->once())->method('isStockAlertAllowed')->will($this->returnValue(true));
        $this->_helper->expects($this->never())->method('getSaveUrl');

        $this->_registry->expects(
            $this->any()
        )->method(
            'registry'
        )->with(
            'current_product'
        )->will(
            $this->returnValue(null)
        );

        $this->_block->setLayout($this->_layout);
        $this->_block->setTemplate('path/to/template.phtml');

        $this->assertEquals('', $this->_block->getTemplate());
        $this->assertNull($this->_block->getSignupUrl());
    }

    public function testEnabledForGuests()
    {
        $this->_helper->expects($this->once())->method('isStockAlertAllowed')->will($this->returnValue(true));

        $this->_product->expects($this->once())->method('isAvailable')->will($this->returnValue(false));

        $this->_digidirectHelper->expects($this->any())->method('isEnabledForGuest')->will($this->returnValue(true));

        $this->_registry->expects(
            $this->any()
        )->method(
            'registry'
        )->with(
            'current_product'
        )->will(
            $this->returnValue($this->_product)
        );

        $this->_block->setLayout($this->_layout);
        $this->_block->setGuestTemplate('path/to/guest_template.phtml');
        $this->_block->setDefaultTemplate('path/to/template.phtml');
        $this->_block->setTemplate('path/to/template.phtml');

        $this->assertEquals('path/to/guest_template.phtml', $this->_block->getTemplate());
    }
}
