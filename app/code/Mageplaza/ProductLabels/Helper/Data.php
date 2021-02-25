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

namespace Mageplaza\ProductLabels\Helper;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\ProductLabels\Model\Meta;
use Mageplaza\ProductLabels\Model\MetaFactory;
use Mageplaza\ProductLabels\Model\Rule;
use Mageplaza\ProductLabels\Model\RuleFactory;

/**
 * Class Data
 * @package Mageplaza\ProductLabels\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'productlabels';
    const VARIABLES          = [
        '{{discount}}',
        '{{discount_percent}}',
        '{{current_price}}',
    ];

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Template
     */
    protected $template;

    /**
     * List product id stop process
     *
     * @var array
     */
    protected $listIdStopProcess = [];

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param RuleFactory $ruleFactory
     * @param MetaFactory $metaFactory
     * @param CollectionFactory $productCollectionFactory
     * @param ProductRepository $productRepository
     * @param Template $template
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        RuleFactory $ruleFactory,
        MetaFactory $metaFactory,
        CollectionFactory $productCollectionFactory,
        ProductRepository $productRepository,
        Template $template
    ) {
        $this->ruleFactory              = $ruleFactory;
        $this->metaFactory              = $metaFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->template                 = $template;
        $this->productRepository        = $productRepository;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param Rule $rule
     * @param Meta $metaModel
     *
     * @return $this
     */
    public function applyRuleId($rule, $metaModel)
    {
        $listId   = $this->getProductIds($rule);
        $storeIds = $rule->getStoreIds();
        if (!is_array($storeIds)) {
            $storeIds = explode(',', $storeIds);
        }

        if (is_array($listId)) {
            $metaData = [];

            foreach ($listId as $proId) {
                foreach ($storeIds as $storeId) {
                    if (!in_array($proId, $this->listIdStopProcess, true)) {
                        $metaData['rule_id']            = $rule->getId();
                        $metaData['product_id']         = $proId;
                        $metaData['template_url']       = $rule->getListTemplate();
                        $metaData['img_url']            = $this->getSrcImg($rule, $storeId);
                        $metaData['label']              = $rule->getListLabel();
                        $metaData['label_style']        = $this->getLabelStyle($rule);
                        $metaData['label_fontsize']     = $rule->getListFontSize();
                        $metaData['label_position']     = $rule->getListPosition();
                        $metaData['custom_css']         = $this->getCategoryCustomCss($rule, $proId);
                        $metaData['from_date']          = strtotime($rule->getFromDate());
                        $metaData['to_date']            = strtotime($rule->getToDate());
                        $metaData['customer_group_ids'] = $rule->getCustomerGroupIds();
                        $metaData['priority']           = $rule->getPriority();

                        if (!empty($metaData)) {
                            $metaModel->applyRule($metaData);
                            $metaData = [];
                        }
                    }
                }
            }
        }

        if ($rule->getStopProcess()) {
            $this->listIdStopProcess = $listId;
        }

        return $this;
    }

    /**
     * @param Rule $rule
     * @param null $storeId
     *
     * @return string|null
     */
    public function getSrcImg($rule, $storeId = null)
    {
        if ($listImage = $rule->getListImage()) {
            $folder = $rule->getSame() ? Image::TEMPLATE_MEDIA_PRODUCT_LABEL : Image::TEMPLATE_MEDIA_LISTING_LABEL;

            return $this->getImageUrl($listImage, $folder, $storeId);
        }

        return null;
    }

    /**
     * @param string $label
     * @param Product $product
     *
     * @return string
     */
    public function getCategoryProductLabel($label, $product)
    {
        $attrCodes = $this->getAttrInLabel($label);
        if (!$product->getPrice()) {
            $product = $this->getProductById($product->getId());
        }
        $replace = [
            $this->getDiscount($product),
            $this->getPercentDiscount($product),
            $this->getCurrentPrice($product),
        ];
        $label   = str_replace(self::VARIABLES, $replace, $label);

        if ($attrCodes && !empty($attrCodes)) {
            foreach ($attrCodes[1] as $code) {
                if (!in_array('{{' . $code . '}}', self::VARIABLES, true)) {
                    $label = str_replace('{{' . $code . '}}', $this->getAttributeProduct($code, $product), $label);
                }
            }
        }

        return $label;
    }

    /**
     * @param Rule $rule
     *
     * @return string
     */
    public function getLabelStyle($rule)
    {
        $font  = $rule->getListFont();
        $color = $rule->getListColor();

        return 'font-family:' . $font . '; color:' . $color;
    }

    /**
     * @param Rule $rule
     * @param int $id
     *
     * @return string
     */
    public function getCategoryCustomCss($rule, $id)
    {
        $customCss = $rule->getListCss();
        $search    = [
            'design-labels-list',
            'design-label-image-list',
            'design-label-text-list',
        ];
        $replace   = [
            'design-labels-' . $rule->getId() . '-' . $id,
            'design-label-image-' . $rule->getId() . '-' . $id,
            'design-label-text-' . $rule->getId() . '-' . $id,
        ];

        return str_replace($search, $replace, $customCss);
    }

    /**
     * @param int $productId
     *
     * @return Product
     */
    public function getProductById($productId)
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * @param Product $product
     *
     * @return string
     */
    public function getDiscount(Product $product)
    {
        $originalPrice  = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
        $finalPrice     = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        $currencySymbol = $this->getCurrentCySymbol();
        $discount       = 0;

        if ($originalPrice > $finalPrice) {
            $discount = $originalPrice - $finalPrice;
        }

        return $currencySymbol . $discount;
    }

    /**
     * @return string
     */
    public function getCurrentCySymbol()
    {
        try {
            return $this->storeManager->getStore()->getCurrentCurrencyCode();
        } catch (NoSuchEntityException $e) {
            $this->_logger->critical($e->getMessage());

            return '';
        }
    }

    /**
     * Get discount percent by product id
     *
     * @param Product $product
     *
     * @return int|string
     */
    public function getPercentDiscount(Product $product)
    {
        $originalPrice = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
        $finalPrice    = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

        $percentage = 0;
        if ($originalPrice > $finalPrice) {
            $percentage = number_format(($originalPrice - $finalPrice) * 100 / $originalPrice);
        }

        return $percentage;
    }

    /**
     * Get Current price by product id
     *
     * @param Product $product
     *
     * @return float
     */
    public function getCurrentPrice(Product $product)
    {
        return number_format($product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue(), 2);
    }

    /**
     * Get product attribute name
     *
     * @param string $attributeCode
     * @param Product $product
     *
     * @return string
     */
    public function getAttributeProduct($attributeCode, $product)
    {
        if (!$product->getId()) {
            return null;
        }

        if ($attr = $product->getAttributeText($attributeCode)) {
            if (is_array($attr)) {
                $attr = implode(',', $attr);
            }

            return $attr;
        }

        if (is_object($product->getCustomAttribute($attributeCode))) {
            $option   = $product->getCustomAttribute($attributeCode);
            $optionId = null;
            if ($option !== null) {
                $optionId = $option->getValue();
            }

            $_attributeId = $product->getResource()->getAttribute($attributeCode);
            if ($optionId && $_attributeId->usesSource()) {
                $label = $_attributeId->getSource()->getOptionText($optionId);
                if (is_array($label)) {
                    return implode(', ', $label);
                }

                return $label;
            }
        }

        return null;
    }

    /**
     * Get attribute code in variables label
     *
     * @param string $label
     *
     * @return mixed
     */
    public function getAttrInLabel($label)
    {
        $re = '/\{{(.*?)\}}/m';
        preg_match_all($re, $label, $matches);

        return $matches;
    }

    /**
     * @param Rule $rule
     * @param null $storeId
     *
     * @return array
     */
    public function getProductIds($rule, $storeId = null)
    {
        $productIds = $rule->getMatchingProductIds($storeId);

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
     * @param $rule
     *
     * @return Phrase
     */
    public function getState($rule)
    {
        if ($rule->getId()) {
            $toDate      = strtotime($rule->getToDate());
            $fromDate    = strtotime($rule->getFromDate());
            $currentDate = strtotime(date('d-m-Y H:i:s'));

            if (($toDate >= $currentDate && $fromDate <= $currentDate) || (!$toDate && $fromDate <= $currentDate)) {
                return __('Running');
            }
            if ($fromDate > $currentDate) {
                return __('Queue');
            }
            if ($toDate < $currentDate) {
                return __('Done');
            }
        }

        return __('None');
    }

    /**
     * @param $image
     * @param string $type
     * @param null $storeId
     *
     * @return string
     */
    public function getImageUrl($image, $type = Image::TEMPLATE_MEDIA_LABEL, $storeId = null)
    {
        try {
            $imageHelper  = $this->getImageHelper();
            $imageFile    = $imageHelper->getMediaPath($image, $type);
            $baseMediaUrl = rtrim($this->storeManager->getStore($storeId)
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA), '/');

            return $baseMediaUrl . '/' . ltrim(str_replace('\\', '/', $imageFile), '/');
        } catch (Exception $e) {
            return '#';
        }
    }

    /**
     * @return Image
     */
    public function getImageHelper()
    {
        return $this->objectManager->get(Image::class);
    }

    /**
     * @param null $storeId
     *
     * @return bool|StoreInterface
     */
    public function getStore($storeId = null)
    {
        try {
            return $this->storeManager->getStore($storeId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @param string $path
     * @param array $params
     *
     * @return string
     */
    public function getTemplateUrl($path, $params = [])
    {
        return $this->template->getViewFileUrl('Mageplaza_ProductLabels::images/template/' . $path, $params);
    }

    /**
     * @param Rule|DataObject $label
     */
    public function processImageUrl($label)
    {
        if ($labelTemplate = $label->getLabelTemplate()) {
            $label->setLabelTemplate($this->getTemplateUrl(
                $labelTemplate,
                ['area' => Area::AREA_FRONTEND]
            ));

            $label->setLabelImage(null);
        } elseif ($labelImage = $label->getLabelImage()) {
            $label->setLabelImage($this->getImageUrl(
                $labelImage,
                Image::TEMPLATE_MEDIA_PRODUCT_LABEL
            ));
        }

        if ($listTemplate = $label->getListTemplate()) {
            $label->setListTemplate($this->getTemplateUrl(
                $listTemplate,
                ['area' => Area::AREA_FRONTEND]
            ));

            $label->setListImage(null);
        } elseif ($listImage = $label->getListImage()) {
            $folder = $label->getSame() ? Image::TEMPLATE_MEDIA_PRODUCT_LABEL : Image::TEMPLATE_MEDIA_LISTING_LABEL;
            $label->setListImage($this->getImageUrl(
                $listImage,
                $folder
            ));
        }
    }
}
