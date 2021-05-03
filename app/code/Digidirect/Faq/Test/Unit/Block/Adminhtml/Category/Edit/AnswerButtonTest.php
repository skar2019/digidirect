<?php
namespace Digidirect\Faq\Test\Unit\Block\Adminhtml\Category\Edit;

use Digidirect\Faq\Block\Adminhtml\Faq\Edit\AnswerButton;
use Digidirect\Faq\Model\Faq;
use Digidirect\Faq\Model\Registry\Constants;
use Digidirect\Faq\Test\Unit\FaqTestUnitTrait;
use Digidirect\Utilities\Test\Unit\Library;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Url;

/**
 * Class AnswerButtonTest
 *
 * @package Digidirect\Faq\Test\Unit\Block\Adminhtml\Category\Edit
 */
class AnswerButtonTest extends Library
{
    /**
     * @var AnswerButton
     */
    protected $buttonOriginal;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    public function setUp()
    {
        parent::setUp();

        $this->registryMock = $this->getMockObjectWithoutConstructor(Registry::class, ['registry']);

        $this->urlMock = $this->getMockObjectWithoutConstructor(Url::class, ['getUrl']);

        $this->contextMock = $this->getMockObjectWithoutConstructor(
            \Magento\Backend\Block\Widget\Context::class,
            ['getUrlBuilder']
        );

        $this->contextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->willReturn($this->urlMock);

        $this->buttonOriginal = $this->objectManager->getObject(
            AnswerButton::class,
            [
                'registry' => $this->registryMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * @dataProvider provideTestData
     * @param [] $config
     * @param string $callsCount
     */
    public function testGetButtonData($config, $callsCount)
    {
        /**
         * @var $faq \PHPUnit_Framework_MockObject_MockObject
         */
        $faq = $config['faq'];

        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with(Constants::CURRENT_FAQ_ITEM)
            ->willReturn($faq);

        $faq->expects($this->any())
            ->method('getId')
            ->willReturn($config['faq_id']);

        $faq->expects($this->any())
            ->method('getAnswer')
            ->willReturn($config['answer']);

        $faq->expects($this->any())
            ->method('getCustomerEmail')
            ->willReturn($config['customer_email']);

        $this->urlMock->expects($this->$callsCount())
            ->method('getUrl');

        $this->buttonOriginal->getButtonData();
    }

    /**
     * @return array
     */
    public function provideTestData()
    {
        $config1 = [
            'faq' => $this->getFaq(),
            'faq_id' => null,
            'answer' => null,
            'customer_email' => null
        ];
        $config3 = [
            'faq' => $this->getFaq(),
            'faq_id' => 1,
            'answer' => null,
            'customer_email' => null
        ];
        $config4 = [
            'faq' => $this->getFaq(),
            'faq_id' => 1,
            'answer' => 1,
            'customer_email' => null
        ];
        $config5 = [
            'faq' => $this->getFaq(),
            'faq_id' => 1,
            'answer' => 1,
            'customer_email' => 2
        ];
        return [
            [$config1, 'never'],
            [$config3, 'never'],
            [$config4, 'never'],
            [$config5, 'once'],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFaq()
    {
        return $this->getMockObjectWithoutConstructor(Faq::class, ['getId', 'getCustomerEmail', 'getAnswer']);
    }
}
