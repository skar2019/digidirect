<?php
namespace Digidirect\Blog\Controller\Adminhtml\Post;

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
        return 'admin.Digidirect_blog.related_posts.grid';
    }
}
