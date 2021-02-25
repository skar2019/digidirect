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

namespace Mageplaza\ProductLabels\Block;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Mageplaza\ProductLabels\Helper\Data as HelperData;
use Mageplaza\ProductLabels\Model\MetaFactory;
use Mageplaza\ProductLabels\Model\ResourceModel\Rule;
use Mageplaza\ProductLabels\Model\ResourceModel\RuleFactory as ResourceRuleFactory;
use Mageplaza\ProductLabels\Model\RuleFactory;

/**
 * Class Label
 * @package Mageplaza\ProductLabels\Block
 */
class Label extends Template
{
    /**
     * @var HelperData
     */
    public $_helperData;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var ResourceRuleFactory
     */
    protected $_resourceRuleFactory;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductFactory
     */
    protected $_productLoader;

    /**
     * @var Gallery
     */
    protected $_gallery;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ThemeProviderInterface
     */
    protected $_themeProvider;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * Label constructor.
     *
     * @param Template\Context $context
     * @param RuleFactory $ruleFactory
     * @param ResourceRuleFactory $resourceRuleFactory
     * @param HelperData $helperData
     * @param CollectionFactory $productCollectionFactory
     * @param ProductFactory $productLoader
     * @param Gallery $gallery
     * @param Registry $registry
     * @param SessionFactory $customerSession
     * @param ThemeProviderInterface $themeProvider
     * @param MetaFactory $metaFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        RuleFactory $ruleFactory,
        ResourceRuleFactory $resourceRuleFactory,
        HelperData $helperData,
        CollectionFactory $productCollectionFactory,
        ProductFactory $productLoader,
        Gallery $gallery,
        Registry $registry,
        SessionFactory $customerSession,
        ThemeProviderInterface $themeProvider,
        MetaFactory $metaFactory,
        array $data = []
    ) {
        $this->_ruleFactory             = $ruleFactory;
        $this->_resourceRuleFactory     = $resourceRuleFactory;
        $this->_helperData              = $helperData;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_productLoader           = $productLoader;
        $this->_gallery                 = $gallery;
        $this->_registry                = $registry;
        $this->customerSession          = $customerSession;
        $this->_themeProvider           = $themeProvider;
        $this->metaFactory              = $metaFactory;

        parent::__construct($context, $data);
    }

    /**
     * Get All Rule Collection apply on product
     *
     * @param $product
     *
     * @return array
     */
    public function getRulesApplyProduct($product)
    {
        /** @var Rule $resourceModel */
        $resourceModel  = $this->_resourceRuleFactory->create();
        $ruleIds        = $resourceModel->getMatchingRuleIds();
        $ruleCollection = [];
        $customerGroup  = 0;
        if ($this->customerSession->create()->isLoggedIn()) {
            $customerGroup = $this->customerSession->create()->getCustomer()->getGroupId();
        }

        foreach ($ruleIds as $ruleId) {
            $rule    = $this->_ruleFactory->create()->load($ruleId);
            $isValid = $rule->getConditions()->validate($product);
            if (!$isValid) {
                continue;
            }
            $customerGroupRule = explode(',', $rule['customer_group_ids']);

            if ($rule['customer_group_ids'] === null || in_array((string) $customerGroup, $customerGroupRule, true)) {
                $ruleCollection[] = $rule;

                if ($rule->getStopProcess()) {
                    return $ruleCollection;
                }
            }
        }

        return $ruleCollection;
    }

    /**
     * Get Product Ids by rule conditions
     *
     * @param $rule
     *
     * @return array
     */
    public function getProductIds($rule)
    {
        $productIds = $rule->getMatchingProductIds();

        if ($rule->getBestseller() !== '1') {
            return $productIds;
        }

        $bestSellerProductIds = [];
        $collection           = $this->productCollectionFactory->create()
            ->addIdFilter($productIds)
            ->setPageSize($rule->getLimit());

        $collection->getSelect()->joinLeft(
            ['soi' => $collection->getTable('sales_order_item')],
            'e.entity_id = soi.product_id',
            ['qty_ordered' => 'SUM(soi.qty_ordered)']
        )->group('e.entity_id')->where('soi.qty_ordered', ['gt' => 0])->order('qty_ordered DESC');

        $collection->addStoreFilter();

        foreach ($collection->getData() as $item) {
            $bestSellerProductIds[] = $item['entity_id'];
        }

        return $bestSellerProductIds;
    }

    /**
     * check validate product in rule
     *
     * @param $rule
     * @param $id
     *
     * @return bool
     */
    public function validateProductInRule($rule, $id)
    {
        return in_array($id, $this->getProductIds($rule), true);
    }

    /**
     * Replace variables label
     *
     * @param string $label
     * @param Product $product
     *
     * @return mixed
     */
    public function replaceLabel($label, Product $product)
    {
        return $this->_helperData->getCategoryProductLabel($label, $product);
    }

    /**
     * get Current Product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Get Url image template
     *
     * @param string $path
     *
     * @return string
     */
    public function getTemplateUrl($path)
    {
        return $this->_helperData->getTemplateUrl($path);
    }

    /**
     * Check Smartwave/porto theme
     *
     * @return bool
     */
    public function isPortoTheme()
    {
        $themeId = $this->_scopeConfig->getValue(
            DesignInterface::XML_PATH_THEME_ID,
            ScopeInterface::SCOPE_STORE,
            $this->_helperData->getStore()->getId()
        );

        /** @var $theme ThemeInterface */
        $theme = $this->_themeProvider->getThemeById($themeId);

        return $theme->getCode() === 'Smartwave/porto';
    }
}
