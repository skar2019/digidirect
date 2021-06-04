<?php
namespace Ewave\NavigationCMSUpgrade\Test\Unit\Plugin\Ewave\CmsUpgrade\Model;

use Ewave\NavigationCMSUpgrade\Plugin\Ewave\CmsUpgrade\Model\SetupProcessFactory as SetupProcessorFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Ewave\NavigationCMSUpgrade\Plugin\Ewave\CmsUpgrade\Model\SetupProcessFactory;
use Ewave\CmsUpgrade\Model\SetupProcessorFactory as CmsUpgradeSetupFactory;

/**
 * Class SetupProcessFactoryTest
 *
 * @package Ewave\NavigationCMSUpgrade\Test\Unit\Plugin\Ewave\CmsUpgrade\Model
 */
class SetupProcessFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var SetupProcessFactory
     */
    protected $pluginOriginal;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * Setup object
     *
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->getMockBuilder(\Magento\Framework\ObjectManager\ObjectManager::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->subjectMock = $this->getMockBuilder(CmsUpgradeSetupFactory::class)
            ->setMethods(['makeProcessor'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->pluginOriginal = $this->objectManager->getObject(
            SetupProcessorFactory::class,
            [
                'objectManager' => $this->objectManagerMock,
                'config' => [
                    'menu_item' => 'Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem\MenuItem',
                ],
            ]
        );
    }

    /**
     * @param string $type
     * @param string $callsCount
     * @dataProvider provideData
     */
    public function testAroundMakeProcessor($type, $callsCount)
    {
        $function = function () {
            return 'some class';
        };

        $this->objectManagerMock->expects($this->$callsCount())
            ->method('get');

        $this->pluginOriginal->aroundMakeProcessor($this->subjectMock, $function, $type);
    }

    /**
     * @return array
     */
    public function provideData()
    {
        return [
            [
                'menu_item', 'once',
            ],
            [
                'some_type', 'never',
            ],
        ];
    }
}
