<?php
namespace Ewave\Faq\Test\Unit\Helper;

use Ewave\Faq\Helper\Question;
use Ewave\Utilities\Test\Unit\Library;
use Magento\Framework\App\Request\DataPersistor;

class QuestionTest extends Library
{
    /**
     * @var Question
     */
    protected $helperOriginal;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataPersistorMock;

    /**
     * Setup objects
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->dataPersistorMock = $this->getMockObjectWithoutConstructor(
            DataPersistor::class,
            ['get', 'set', 'clear']
        );

        $this->helperOriginal = $this->objectManager->getObject(
            Question::class,
            [
                'dataPersistor' => $this->dataPersistorMock,
            ]
        );
    }

    /**
     * @dataProvider provide()
     * @param array $config
     * @param string $expected
     */
    public function testGetPostValue(array $config, $expected)
    {
        $this->dataPersistorMock->expects($this->any())
            ->method('get')
            ->with(Question::POST_VALUE_KEY)
            ->willReturn($config);

        $this->assertEquals($expected, $this->helperOriginal->getPostValue($config['key']));
    }

    /**
     * @return array
     */
    public function provide()
    {
        return [
            [['key' => 'string', 'string' => 'string' ], 'string'],
            [['key' => 'b', 'b' => 'second string'], 'second string'],
            [['key' => 'm'], '']
        ];
    }
}
