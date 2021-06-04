<?php


namespace Ewave\InfiniteScroll\Model\Config;


class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = ['/config/scroll' => 'name'];
}
