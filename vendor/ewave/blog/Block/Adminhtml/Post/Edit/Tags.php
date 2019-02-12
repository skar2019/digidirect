<?php
namespace Ewave\Blog\Block\Adminhtml\Post\Edit;

use Ewave\Blog\Api\Data\PostInterface;
use Ewave\Blog\Model\ResourceModel\Tag\CollectionFactory;

/**
 * Class Tags
 */
class Tags extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Ewave_Blog::post/tags.phtml';

    /**
     * @var CollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Tags constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CollectionFactory $tagCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        CollectionFactory $tagCollectionFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->tagCollectionFactory = $tagCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Ewave\Blog\Model\ResourceModel\Tag\Collection
     */
    public function getCollection()
    {
        $post = $this->registry->registry(PostInterface::CURRENT_ITEM);
        $collection = $this->tagCollectionFactory->create();
        if (!empty($post->getTagId())) {
            $collection->addFieldToFilter('entity_id', ['nin' => $post->getTagId()]);
        }
        return $collection;
    }
}
