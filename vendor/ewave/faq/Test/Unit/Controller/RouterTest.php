<?php
namespace Ewave\Faq\Test\Unit\Controller;

use Ewave\Faq\Test\Unit\FaqTestUnitTrait;

/**
 * Class RouterTest
 * @package Ewave\Faq\Test\Unit\Controller
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    use FaqTestUnitTrait;

    /**
     * @var \Ewave\Faq\Controller\Router
     */
    protected $_model;

    public function testMatch()
    {
        $url = 'faq';
        $request = $this->_getRequestMockWithoutConstructor(['getPathInfo']);
        $request->method('getPathInfo')->willReturn($url);
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $actionFactory = $this->getMock('Magento\Framework\App\ActionFactory', [], [], '', false);
        $actionFactory->expects($this->once())->method('create')->with(
            'Magento\Framework\App\Action\Forward'
        )->will(
            $this->returnValue(
                $this->getMockForAbstractClass('Magento\Framework\App\Action\AbstractAction', [], '', false)
            )
        );
        $faqHelper = $this->getMockObjectWithoutConstructor('Ewave\Faq\Helper\Data', ['isModuleEnabled', 'isValidUrl']);
        $faqHelper->method('isModuleEnabled')->willReturn(1);
        $faqHelper->method('isValidUrl')->willReturn(true);

        $this->_model = $helper->getObject(
            'Ewave\Faq\Controller\Router',
            [
                'actionFactory' => $actionFactory,
                'faqHelper' => $faqHelper
            ]
        );

        $this->assertInstanceOf('Magento\Framework\App\Action\AbstractAction', $this->_model->match($request));
        $this->assertEquals($url, $request->getModuleName());
        $this->assertEquals('index', $request->getControllerName());
        $this->assertEquals('index', $request->getActionName());
    }
}
