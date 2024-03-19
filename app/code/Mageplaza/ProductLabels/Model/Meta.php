<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Model;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Meta
 * @package Mageplaza\ProductLabels\Model
 */
class Meta extends AbstractModel
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var ResourceModel\Meta
     */
    protected $resourceMeta;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * Meta constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     * @param ResourceModel\Meta $resourceMeta
     * @param Session $customerSession
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        ResourceModel\Meta $resourceMeta,
        Session $customerSession,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager     = $storeManager;
        $this->localeDate       = $localeDate;
        $this->resourceMeta     = $resourceMeta;
        $this->_customerSession = $customerSession;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Meta::class);
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getRulesFromProduct(Product $product)
    {
        $rulesData = [];
        try {
            $productId = $product->getId();
            $storeId   = $product->getStoreId() ?: $this->storeManager->getStore()->getId();
            if ($product->hasCustomerGroupId()) {
                $customerGroupId = $product->getCustomerGroupId();
            } else {
                $customerGroupId = $this->_customerSession->getCustomerGroupId();
            }
            $dateTs = $this->localeDate->scopeTimeStamp($storeId);

            $rulesData = $this->resourceMeta->_getRulesFromProduct($dateTs, $storeId, $customerGroupId, $productId);
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        return $rulesData;
    }

    /**
     * @param Product $product
     * @param int $ruleId
     *
     * @return array
     */
    public function getRulesFromProductWidget(Product $product, $ruleId)
    {
        $rulesData = [];
        try {
            $productId = $product->getId();
            $storeId   = $product->getStoreId() ?: $this->storeManager->getStore()->getId();
            if ($product->hasCustomerGroupId()) {
                $customerGroupId = $product->getCustomerGroupId();
            } else {
                $customerGroupId = $this->_customerSession->getCustomerGroupId();
            }
            $dateTs = $this->localeDate->scopeTimeStamp($storeId);

            $rulesData = $this->resourceMeta->_getRulesFromProductWidget(
                $dateTs,
                $storeId,
                $customerGroupId,
                $productId,
                $ruleId
            );
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        return $rulesData;
    }

    /**
     * @param $ruleId
     *
     * @return array
     */
    public function getRuleFromRuleId($ruleId)
    {
        $rulesData = [];
        try {
            $storeId         = $this->storeManager->getStore()->getId();
            $customerGroupId = $this->_customerSession->getCustomerGroupId();
            $dateTs          = $this->localeDate->scopeTimeStamp($storeId);

            $rulesData = $this->resourceMeta->getRuleFromRuleId($dateTs, $storeId, $customerGroupId, $ruleId);
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        return $rulesData;
    }

    /**
     * Apply rule
     *
     * @param array $data
     *
     * @return mixed
     */
    public function applyRule($data)
    {
        return $this->resourceMeta->applyRule($data);
    }

    /**
     * return @voild
     */
    public function truncateData()
    {
        return $this->resourceMeta->truncateData();
    }
}
