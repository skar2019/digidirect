<?php
namespace Ewave\Blog\Controller\Adminhtml\Post;

/**
 * Class Posts
 */
class Posts extends AbstractAjaxGird
{
    /**
     * @return string
     */
    protected function getBlockName()
    {
        return 'admin.ewave_blog.related_posts.grid';
    }
}
