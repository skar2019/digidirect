<?php
namespace Ewave\Faq\Block;

use Ewave\Faq\Model\Category;

/**
 * Class Overview
 * @package Ewave\Faq\Block
 */
class Overview extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ewave\Faq\Helper\Data
     */
    protected $_faqHelper;

    /**
     * @var \Ewave\Faq\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * Overview constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ewave\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Ewave\Faq\Helper\Data $faqHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ewave\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Ewave\Faq\Helper\Data $faqHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_faqHelper = $faqHelper;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->setDescription('');
        $this->pageConfig->setKeywords('');
    }

    /**
     * @return $this
     */
    public function getAllCategory()
    {
        $categories = $this->_categoryCollectionFactory->create();
        return $categories->getCategoryCollection();
    }

    /**
     * Get URL for ajax call
     *
     * @return string
     */
    public function getFaqAjaxUrl()
    {
        return $this->getUrl(
            $this->_faqHelper->getAjaxCallUrl(),
            [
                '_secure' => $this->getRequest()->isSecure(),
            ]
        );
    }

    /**
     * Retrieve faq type from request
     *
     * @return mixed
     */
    public function getFaqType()
    {
        return $this->getRequest()->getParam('faqType');
    }

    /**
     * Retrieve faq id from request
     *
     * @return mixed
     */
    public function getFaqId()
    {
        return $this->getRequest()->getParam('faqId');
    }
}
