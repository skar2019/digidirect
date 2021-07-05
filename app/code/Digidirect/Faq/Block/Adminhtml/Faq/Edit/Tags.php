<?php
namespace Digidirect\Faq\Block\Adminhtml\Faq\Edit;

use Digidirect\Faq\Model\ResourceModel\Tag\CollectionFactory;
use Digidirect\Faq\Model\Registry\Constants;

/**
 * Class Tags
 * @package Digidirect\Faq\Block\Adminhtml\Faq\Edit
 */
class Tags extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Digidirect_Faq::faq/tags.phtml';

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
     * @return \Digidirect\Faq\Model\ResourceModel\Tag\Collection
     */
    public function getCollection()
    {
        $faq = $this->registry->registry(Constants::CURRENT_FAQ_ITEM);
        $collection = $this->tagCollectionFactory->create();
        if (!empty($faq->getTagId())) {
            $collection->addFieldToFilter('entity_id', ['nin' => $faq->getTagId()]);
        }
        return $collection;
    }
}
