<?php

namespace Ewave\FreeGift\Model\Rule;

class Validator extends \Magento\Rule\Model\AbstractModel
{
    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Action\CollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var []
     */
    protected $_productIds = [];

    /**
     * Validator constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\Rule\Condition\CombineFactory $combineFactory
     * @param \Magento\SalesRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $relatedCacheTypes
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $combineFactory,
        \Magento\SalesRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $relatedCacheTypes = [],
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Getter for rule conditions collection
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    /**
     * Getter for rule actions collection
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Ewave\FreeGift\Model\Rule $salesRule
     * @param int $websiteId
     * @return bool
     */
    public function validateProduct(
        \Magento\Catalog\Model\Product $product,
        $salesRule,
        $websiteId
    ) {
        $cacheKey = $salesRule->getId() . '_' . $product->getId();
        if (!isset($this->_productIds[$cacheKey])) {
            $this->_resetConditions();
            $this->setConditionsSerialized($salesRule->getConditionsSerialized());

            $product->setAllItems([$product]);
            $product->setProduct($product);
            $product->setProductId($product->getId());

            $validationData = [
                'product' => $product,
                'row' => $product->getData(),
            ];

            $this->_productIds[$cacheKey] = $this->callbackValidateProduct($validationData);
        }

        return $this->_productIds[$cacheKey][$websiteId] ?? false;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return array
     */
    protected function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        $websites = $this->_getWebsitesMap();
        $results = [];

        foreach ($websites as $websiteId => $defaultStoreId) {
            $product->setStoreId($defaultStoreId);
            $results[$websiteId] = $this->getConditions()->validate($product);
        }

        return $results;
    }

    /**
     * Prepare website map
     *
     * @return array
     */
    protected function _getWebsitesMap()
    {
        $map = [];
        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            // Continue if website has no store to be able to create catalog rule for website without store
            if ($website->getDefaultStore() === null) {
                continue;
            }
            $map[$website->getId()] = $website->getDefaultStore()->getId();
        }
        return $map;
    }
}
