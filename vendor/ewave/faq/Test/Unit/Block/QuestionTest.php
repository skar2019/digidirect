<?php
namespace Ewave\Faq\Test\Unit\Block;

use Ewave\Faq\Block\Question;
use Ewave\Utilities\Test\Unit\Library;

class QuestionTest extends Library
{
    /**
     * @var Question
     */
    protected $blockOriginal;

    /**
     * Setup objects
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->blockOriginal = $this->objectManager->getObject(Question::class);
    }

    /**
     * @return void
     */
    public function testIsScopePrivate()
    {
        $this->assertTrue($this->blockOriginal->isScopePrivate());
    }
}
