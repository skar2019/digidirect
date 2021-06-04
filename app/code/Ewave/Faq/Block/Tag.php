<?php
namespace Ewave\Faq\Block;

/**
 * Class Tag
 * @package Ewave\Faq\Block
 */
class Tag extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ewave\Faq\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $_tagCollectionFactory;

    /**
     * Tag constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ewave\Faq\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ewave\Faq\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        array $data = []
    ) {
        $this->_tagCollectionFactory = $tagCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getAllTags()
    {
        $tagCollection = $this->_tagCollectionFactory->create();
        $tagCollection->addFilterByUsingTags();
        return $tagCollection;
    }
}
