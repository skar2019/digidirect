<?php
namespace Ewave\Faq\Block;

use Ewave\Faq\Model\Faq;

/**
 * Class Listfaq
 * @package Ewave\Faq\Block
 */
class Listfaq extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ewave\Faq\Helper\Data
     */
    protected $_faqHelper;
    
    /**
     * @var \Ewave\Faq\Model\ResourceModel\Faq\CollectionFactory
     */
    protected $_faqCollectionFactory;

    /**
     * @var \Ewave\Faq\Model\FaqFactory
     */
    protected $_faqFactory;

    /**
     * @var \Ewave\Faq\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var null|string
     */
    protected $_faqType;

    /**
     * @var null|string
     */
    protected $_faqId;

    /**
     * Listfaq constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ewave\Faq\Helper\Data $faqHelper
     * @param \Ewave\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory
     * @param \Ewave\Faq\Model\FaqFactory $faqFactory
     * @param \Ewave\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ewave\Faq\Helper\Data $faqHelper,
        \Ewave\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
        \Ewave\Faq\Model\FaqFactory $faqFactory,
        \Ewave\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->_faqHelper = $faqHelper;
        $this->_faqCollectionFactory = $faqCollectionFactory;
        $this->_faqFactory = $faqFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function getFaqCollection()
    {
        if (!$this->getCollection()) {
            $this->_faqType = $this->getRequest()->getParam('faqType');
            $this->_faqId = $this->getRequest()->getParam('faqId');
            switch ($this->_faqType) {
                case 'category':
                    $collection = $this->getCategoryFaq($this->_faqId);
                    break;
                case 'search':
                    $collection = $this->getSearchResult($this->_faqId);
                    break;
                case 'tag':
                    $collection = $this->getTagFaq($this->_faqId);
                    break;
                default:
                    $category = $this->_categoryCollectionFactory->create();
                    $firstCategory = $category->getCategoryCollection()->setPageSize(1)->getFirstItem();
                    $this->_faqId = $firstCategory->getId();
                    $collection = $this->getCategoryFaq($firstCategory->getId());
            }
            $collection->setOrder('ordering', 'ASC');
            $this->setCollection($collection);
        }
        return $this->getCollection();
    }

    /**
     * @param int $categoryId
     * @return $this
     */
    public function getCategoryFaq($categoryId)
    {
        $category = $this->_faqCollectionFactory->create()
            ->addFilterByCategory($categoryId)
            ->addFieldToFilter('status', Faq::STATUS_ACTIVE);
        return $category;
    }

    /**
     * @param string $keyword
     * @return $this
     */
    public function getSearchResult($keyword)
    {
        $keyword = addslashes($keyword);
        $result = $this->_faqCollectionFactory->create()
            ->addFieldToFilter('status', Faq::STATUS_ACTIVE)
            ->addFieldToFilter(
                ['answer', 'question'],
                [['like' => '%' . $keyword . '%'], ['like' => '%' . $keyword . '%']]
            );
        return $result;
    }

    /**
     * @param string $tag
     * @return $this
     */
    public function getTagFaq($tag)
    {
        $faq = $this->_faqCollectionFactory->create()
            ->addFilterByTag($tag)
            ->addFieldToFilter('status', Faq::STATUS_ACTIVE);
        return $faq;
    }
    
    /**
     * Prepare faq list toolbar
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Ewave\Faq\Block\Pager $toolbar */
        $toolbar = $this->getLayout()->getBlock('faq_list.toolbar');
        $toolbar->setLimit($this->_faqHelper->getQuestionsPerPage());
        if ($toolbar) {
            $collection = $this->getFaqCollection();
            $toolbar->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        return $this;
    }
}
