<?php

namespace Ewave\Faq\Block;

use Ewave\Faq\Model\Category;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Overview
 *
 * @package Ewave\Faq\Block
 */
class Overview extends \Magento\Framework\View\Element\Template implements IdentityInterface
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
     * @var null| \Ewave\Faq\Model\ResourceModel\Category\Collection
     */
    private $allCategory = null;

    /**
     * Overview constructor.
     *
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
        return $this;
    }

    /**
     * @return \Ewave\Faq\Model\ResourceModel\Category\Collection|null
     */
    public function getAllCategory()
    {
        if (null === $this->allCategory) {
            $this->allCategory = $this->_categoryCollectionFactory->create()->getCategoryCollection();
        }
        return $this->allCategory;
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

    /**
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        $allCategory = $this->getAllCategory();
        foreach ($allCategory as $category) {
            /**
             * @var $category Category
             */
            $identities[] = Category::FAQ_CATEGORY_IDENTITY_KEY . '_' . $category->getId();
        }

        return $identities;
    }
}
