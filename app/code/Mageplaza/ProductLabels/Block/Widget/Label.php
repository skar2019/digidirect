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

namespace Mageplaza\ProductLabels\Block\Widget;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\ProductLabels\Block\Label as AbstractLabel;
use Mageplaza\ProductLabels\Helper\Data as HelperData;
use Mageplaza\ProductLabels\Model\MetaFactory;
use Mageplaza\ProductLabels\Model\ResourceModel\RuleFactory as ResourceRuleFactory;
use Mageplaza\ProductLabels\Model\Rule;
use Mageplaza\ProductLabels\Model\RuleFactory;

/**
 * Class Label
 * @package Mageplaza\ProductLabels\Block\Product
 */
class Label extends AbstractLabel
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Label constructor.
     *
     * @param Context $context
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
     * @param ProductRepositoryInterface $productRepository
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
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->productRepository = $productRepository;

        parent::__construct(
            $context,
            $ruleFactory,
            $resourceRuleFactory,
            $helperData,
            $productCollectionFactory,
            $productLoader,
            $gallery,
            $registry,
            $customerSession,
            $themeProvider,
            $metaFactory,
            $data
        );
    }

    /**
     * @param int $ruleId
     *
     * @return Rule
     */
    public function getRuleById($ruleId)
    {
        return $this->_ruleFactory->create()->load($ruleId);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        try {
            $storeId = $this->_helperData->getStore()->getId();
        } catch (Exception $e) {
            $storeId = 0;
        }

        return $storeId;
    }

    /**
     * @param int $productId
     *
     * @return bool|ProductInterface
     */
    public function getProductById($productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param array $dataLabels
     *
     * @return array
     */
    public function prepareDataLabels($dataLabels)
    {
        $storeId       = $this->getStoreId();
        $newDataLabels = [];
        if ($dataLabels) {
            $ruleIds = array_unique(array_column($dataLabels, 'rule_id'));
            foreach ($dataLabels as $dataLabel) {
                if (in_array($dataLabel['rule_id'], $ruleIds, true)) {
                    if ((int) $storeId === (int) $dataLabel['store_id']) {
                        $newDataLabels[] = $dataLabel;
                    } else {
                        $newRuleIds = array_unique(array_column($newDataLabels, 'rule_id'));
                        if (!in_array($dataLabel['rule_id'], $newRuleIds, true)) {
                            $newDataLabels[] = $dataLabel;
                        }
                    }
                }
            }

            return $newDataLabels;
        }

        return $dataLabels;
    }

    /**
     * @param Product $product
     * @param int $ruleId
     *
     * @return array
     */
    public function getDataLabels(Product $product, $ruleId)
    {
        if (!$product->isSaleable() || (int) $product->getIsInStock() === 0) {
            if ($this->_helperData->checkStockStatus($product)) {
                $ruleData  = $this->metaFactory->create()->getRuleFromRuleId($ruleId);
                $labelData = [];

                if (!empty($ruleData)) {
                    foreach ($ruleData as $data) {
                        if ($data['product_id'] === $product->getId()) {
                            $labelData[] = $data;
                        }
                    }
                }

                return $labelData;
            }
        }

        return $this->metaFactory->create()->getRulesFromProductWidget($product, $ruleId);
    }

    /**
     * @param int $ruleId
     *
     * @return string
     */
    public function getWidgetProductTooltip($ruleId)
    {
        return $this->getProductTooltip($ruleId);
    }

    /**
     * @param int $ruleId
     *
     * @return string
     */
    public function getProductTooltip($ruleId)
    {
        $rule               = $this->_ruleFactory->create()->load($ruleId);
        $productTooltip     = HelperData::jsonDecode($rule->getProductTooltip());
        $listProductTooltip = HelperData::jsonDecode($rule->getListProductTooltip());
        $storeCode          = $this->_helperData->getStore()->getCode();
        if ($rule->getSame()) {
            $productTooltips = $this->escapeHtml(isset($productTooltip[$storeCode])
            && $productTooltip[$storeCode] ?
                $productTooltip[$storeCode] : ($productTooltip['admin'] ?: ''));
        } else {
            $productTooltips = $this->escapeHtml(isset($listProductTooltip[$storeCode])
            && $listProductTooltip[$storeCode] ?
                $listProductTooltip[$storeCode] : ($listProductTooltip['admin'] ?: ''));
        }

        return $productTooltips;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function getLabelPosition($data)
    {
        $fontSize = $data['label_fontsize'];
        $posData  = HelperData::jsonDecode($data['label_position']);
        $width    = $posData['label']['width'] * 100 / $this->getProductImgWidth();
        $height   = $posData['label']['height'] * 100 / $this->getProductImgHeight();
        $top      = (($this->getProductImgHeight() - $posData['label']['height']) *
                $posData['label']['percentTop'] / 100) / $this->getProductImgHeight() * 100;
        $left     = (($this->getProductImgWidth() - $posData['label']['width']) *
                $posData['label']['percentLeft'] / 100) / $this->getProductImgWidth() * 100;

        return sprintf(
            'width: %s%%; height: %s%%; top: %s%%; left: %s%%; font-size: %spx',
            $width,
            $height,
            $top,
            $left,
            $fontSize
        );
    }

    /**
     * @return string
     */
    public function getProductImgWidth()
    {
        return $this->_gallery->getImageAttribute('category_page_list', 'width', 1);
    }

    /**
     * @return string
     */
    public function getProductImgHeight()
    {
        return $this->_gallery->getImageAttribute('category_page_list', 'height', 1);
    }

    /**
     * @param array $data
     *
     * @return bool|string
     */
    public function getImgSrc($data)
    {
        if ($data['template_url']) {
            return $this->getTemplateUrl($data['template_url']);
        }

        if ($data['img_url']) {
            return $data['img_url'];
        }

        return false;
    }
}
