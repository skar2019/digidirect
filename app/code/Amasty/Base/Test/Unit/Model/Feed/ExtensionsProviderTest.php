<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/


namespace Amasty\Base\Test\Unit\Model\Feed;

use Amasty\Base\Model\Feed\ExtensionsProvider;
use Amasty\Base\Model\Feed\FeedTypes\Extensions;

class ExtensionsProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getFeedModuleDataDataProvider
     */
    public function testGetFeedModuleData($modules, $expected)
    {
        $extensionsFeed = $this->createMock(Extensions::class);
        $extensionsFeed->expects($this->once())->method('execute')->willReturn($modules);
        $extensionsProvider = new ExtensionsProvider($extensionsFeed);

        $this->assertEquals($expected, $extensionsProvider->getFeedModuleData('test1'));
    }

    public function getFeedModuleDataDataProvider()
    {
        return [
            [[], []],
            [['test1' => 'test1', 'test2' => 'test2'], 'test1']
        ];
    }
}
