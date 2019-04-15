<?php
namespace Ewave\Faq\Model;

use Ewave\Faq\Api\Data\CategoryInterface;

/**
 * Class Category
 * @package Ewave\Faq\Model
 */
class Category extends \Magento\Framework\Model\AbstractModel implements CategoryInterface
{
    const STATUS_ACTIVE = 1;
    const URL_REWRITE_ENTITY_TYPE = 'faq-category';
    const CANONICAL_URL_PATH = 'faq/index/index/faqType/category/faqId/';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'ewave_faq_category';

    /**
     * @var int|null
     */
    protected $_storeViewId = null;

    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ResourceModel\Category
     */
    protected $_categoryResource;

    /**
     * @var \Ewave\Faq\Helper\Data
     */
    protected $_helper;
    
    /**
     * Category constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Category $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ResourceModel\Category\CollectionFactory $resourceCollectionFactory
     * @param \Ewave\Faq\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ewave\Faq\Model\ResourceModel\Category $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ewave\Faq\Model\ResourceModel\Category\CollectionFactory $resourceCollectionFactory,
        \Ewave\Faq\Helper\Data $helper
    ) {
        $resourceCollection = $resourceCollectionFactory->create();
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_categoryResource = $resource;
        if ($storeViewId = $this->_storeManager->getStore()->getId()) {
            $this->_storeViewId = $storeViewId;
        }
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : (array)$this->getData('store_id');
    }

    /**
     * @return bool
     */
    public function validateName()
    {
        $category = $this->_categoryResource->loadByTitle($this->getTitle());
        if ($category !== false) {
            return $category['entity_id'] == $this->getId();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function canBeDeleted()
    {
        $faqIds = $this->_categoryResource->lookupQuestionIds($this->getId());
        return empty($faqIds);
    }

    /**
     * Retrieve category request path
     *
     * @return string
     */
    public function getRequestPath()
    {
        return $this->_helper->getFaqUrl() . '/' . $this->getIdentifier();
    }
}
