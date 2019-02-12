<?php
namespace Ewave\Blog\Controller\Adminhtml\Post;

/**
 * Class Products
 */
class Products extends AbstractAjaxGird
{
    /**
     * @return string
     */
    protected function getBlockName()
    {
        return 'admin.ewave_blog.related_products.grid';
    }
}
