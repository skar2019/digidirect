<?php
namespace Ewave\BlogInfiniteScroll\Model\BlogCategory;

use Ewave\BlogInfiniteScroll\Model\Blog\Processor as BlogProcessor;

/**
 * Class Processor
 *
 * @package Ewave\BlogInfiniteScroll\Model\BlogCategory
 */
class Processor extends BlogProcessor
{
    const BLOCK_NAME = 'blog.category';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->configHelper->isCategoryEnabled();
    }
}
