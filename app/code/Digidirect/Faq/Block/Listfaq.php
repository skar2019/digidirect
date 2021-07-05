<?php
namespace Digidirect\Faq\Block;

use Digidirect\Faq\Model\Faq;

/**
 * Class Listfaq
 * @package Digidirect\Faq\Block
 */
class Listfaq extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Digidirect\Faq\Helper\Data
     */
    protected $_faqHelper;
    
    /**
     * @var \Digidirect\Faq\Model\ResourceModel\Faq\CollectionFactory
     */
    protected $_faqCollectionFactory;

    /**
     * @var \Digidirect\Faq\Model\FaqFactory
     */
    protected $_faqFactory;

    /**
     * @var \Digidirect\Faq\Model\ResourceModel\Category\CollectionFactory
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
     * @param \Digidirect\Faq\Helper\Data $faqHelper
     * @param \Digidirect\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory
     * @param \Digidirect\Faq\Model\FaqFactory $faqFactory
     * @param \Digidirect\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Digidirect\Faq\Helper\Data $faqHelper,
        \Digidirect\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
        \Digidirect\Faq\Model\FaqFactory $faqFactory,
        \Digidirect\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
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
        /** @var \Digidirect\Faq\Block\Pager $toolbar */
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
