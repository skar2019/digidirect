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

use Magento\Backend\Model\Session;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogRule\Model\Rule\Condition\Combine;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Magento\Rule\Model\Action\Collection;
use Mageplaza\ProductLabels\Api\Data\LabelInterface;
use Mageplaza\ProductLabels\Helper\Data as HelperData;

/**
 * Class Rule
 * @package Mageplaza\ProductLabels\Model
 */
class Rule extends AbstractModel implements LabelInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_productlabels_rule';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_productlabels_rule';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_productlabels_rule';

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Session
     */
    protected $_backendSession;

    /**
     * Store matched product Ids with rule id
     *
     * @var array
     */
    protected $dataProductIds;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Iterator
     */
    protected $resourceIterator;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Rule constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param RequestInterface $request
     * @param Session $backendSession
     * @param ProductFactory $productFactory
     * @param CollectionFactory $productCollectionFactory
     * @param HelperData $helperData
     * @param Iterator $resourceIterator
     * @param AbstractDb|null $resourceCollection
     * @param AbstractResource|null $resource
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        RequestInterface $request,
        Session $backendSession,
        ProductFactory $productFactory,
        CollectionFactory $productCollectionFactory,
        HelperData $helperData,
        Iterator $resourceIterator,
        AbstractDb $resourceCollection = null,
        AbstractResource $resource = null
    ) {
        $this->_request                 = $request;
        $this->_backendSession          = $backendSession;
        $this->productFactory           = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_helperData              = $helperData;
        $this->resourceIterator         = $resourceIterator;

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @param null $storeId
     *
     * @return array|null
     */
    public function getMatchingProductIds($storeId = null)
    {
        if ($this->_productIds === null) {
            $this->_productIds = [];
            $this->setCollectedAttributes([]);

            /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter(
                    'visibility',
                    [Visibility::VISIBILITY_BOTH, Visibility::VISIBILITY_IN_CATALOG, Visibility::VISIBILITY_IN_SEARCH]
                )
                ->addAttributeToFilter('status', Status::STATUS_ENABLED)
                ->addStoreFilter($storeId);

            $this->getConditions()->collectValidatedAttributes($productCollection);
            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product'    => $this->productFactory->create(),
                ]
            );
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param $args
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Combine|Collection|\Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->getActionsInstance();
    }

    /**
     * @return Combine|Collection
     */
    public function getActionsInstance()
    {
        return ObjectManager::getInstance()->create(Combine::class);
    }

    /**
     * Get Label Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getData(self::NAME);
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Is enable label
     *
     * @return int
     */
    public function getEnabled()
    {
        return $this->_getData('enabled');
    }

    /**
     * @param int $isEnable
     *
     * @return $this
     */
    public function setEnabled($isEnable)
    {
        return $this->setData('enabled', $isEnable);
    }

    /**
     * Get Store apply label
     *
     * @return string
     */
    public function getStoreIds()
    {
        return $this->_getData('store_ids');
    }

    /**
     * @param string $storeIds
     *
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData('store_ids', $storeIds);
    }

    /**
     * Get Customer Ids apply label
     *
     * @return string
     */
    public function getCustomerGroupIds()
    {
        return $this->_getData('customer_group_ids');
    }

    /**
     * @param string $customerGroupIds
     *
     * @return $this
     */
    public function setCustomerGroupIds($customerGroupIds)
    {
        return $this->setData('customer_group_ids', $customerGroupIds);
    }

    /**
     * Get Priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->_getData('priority');
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        return $this->setData('priority', $priority);
    }

    /**
     * Get Label Template
     *
     * @return string
     */
    public function getLabelTemplate()
    {
        return $this->_getData(self::LABEL_TEMPLATE);
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setLabelTemplate($template)
    {
        return $this->setData(self::LABEL_TEMPLATE, $template);
    }

    /**
     * Get label template
     *
     * @return string
     */
    public function getLabelImage()
    {
        return $this->_getData(self::LABEL_IMAGE);
    }

    /**
     * @param int $path
     *
     * @return $this
     */
    public function setLabelImage($path)
    {
        return $this->setData(self::LABEL_IMAGE, $path);
    }

    /**
     * Get Label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_getData(self::LABEL);
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * Get Label Front
     *
     * @return string
     */
    public function getLabelFront()
    {
        return $this->_getData(self::LABEL_FONT);
    }

    /**
     * @param string $font
     *
     * @return $this
     */
    public function setLabelFront($font)
    {
        return $this->setData(self::LABEL_FONT, $font);
    }

    /**
     * Get Label Front Size
     *
     * @return string
     */
    public function getLabelFontSize()
    {
        return $this->_getData(self::LABEL_FONTSIZE);
    }

    /**
     * @param string $fontSize
     *
     * @return $this
     */
    public function setLabelFontSize($fontSize)
    {
        return $this->setData(self::LABEL_FONTSIZE, $fontSize);
    }

    /**
     * Get Label Front
     *
     * @return string
     */
    public function getLabelFont()
    {
        return $this->_getData(self::LABEL_FONT);
    }

    /**
     * @param string $font
     *
     * @return Rule
     */
    public function setLabelFont($font)
    {
        return $this->setData(self::LABEL_FONT, $font);
    }

    /**
     * Get Label Color
     *
     * @return string
     */
    public function getLabelColor()
    {
        return $this->_getData(self::LABEL_COLOR);
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setLabelColor($color)
    {
        return $this->setData(self::LABEL_COLOR, $color);
    }

    /**
     * @return string
     */
    public function getLabelCss()
    {
        return $this->_getData(self::LABEL_CSS);
    }

    /**
     * @param string $css
     *
     * @return $this
     */
    public function setLabelCss($css)
    {
        return $this->setData(self::LABEL_CSS, $css);
    }

    /**
     * Get Label Position
     *
     * @return string
     */
    public function getLabelPosition()
    {
        return $this->_getData(self::LABEL_POSITION);
    }

    /**
     * @param string $position
     *
     * @return $this
     */
    public function setLabelPosition($position)
    {
        return $this->setData(self::LABEL_POSITION, $position);
    }

    /**
     * Get Label Position on Grid
     *
     * @return string
     */
    public function getLabelPositionGrid()
    {
        return $this->_getData(self::LABEL_POSITION_GRID);
    }

    /**
     * @param string $position
     *
     * @return $this
     */
    public function setLabelPositionGrid($position)
    {
        return $this->setData(self::LABEL_POSITION_GRID, $position);
    }

    /**
     * Get Category is same Product
     *
     * @return int
     */
    public function getSame()
    {
        return $this->_getData('same');
    }

    /**
     * @param int $isSame
     *
     * @return $this
     */
    public function setSame($isSame)
    {
        return $this->setData('same', $isSame);
    }

    /**
     * Get Category Label Template
     *
     * @return string
     */
    public function getListTemplate()
    {
        return $this->_getData(self::LIST_TEMPLATE);
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setListTemplate($template)
    {
        return $this->setData(self::LIST_TEMPLATE, $template);
    }

    /**
     * Get Category Label Image
     *
     * @return string
     */
    public function getListImage()
    {
        return $this->_getData(self::LIST_IMAGE);
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setListImage($path)
    {
        return $this->setData(self::LIST_IMAGE, $path);
    }

    /**
     * Get Category Label
     *
     * @return string
     */
    public function getListLabel()
    {
        return $this->_getData(self::LIST_LABEL);
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setListLabel($label)
    {
        return $this->setData(self::LIST_LABEL, $label);
    }

    /**
     * Get Category Label font
     *
     * @return string
     */
    public function getListFont()
    {
        return $this->_getData(self::LIST_FONT);
    }

    /**
     * @param string $front
     *
     * @return $this
     */
    public function setListFont($front)
    {
        return $this->setData(self::LIST_FONT, $front);
    }

    /**
     * Get Category label Front Size
     *
     * @return string
     */
    public function getListFontSize()
    {
        return $this->_getData(self::LIST_FONT_SIZE);
    }

    /**
     * @param string $frontSize
     *
     * @return $this
     */
    public function setListFontSize($frontSize)
    {
        return $this->setData(self::LIST_FONT_SIZE, $frontSize);
    }

    /**
     * Get category label color
     *
     * @return string
     */
    public function getListColor()
    {
        return $this->_getData(self::LIST_COLOR);
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function setListColor($color)
    {
        return $this->setData(self::LIST_COLOR, $color);
    }

    /**
     * Get category label css
     *
     * @return string
     */
    public function getListCss()
    {
        return $this->_getData(self::LIST_CSS);
    }

    /**
     * @param string $css
     *
     * @return $this
     */
    public function setListCss($css)
    {
        return $this->setData(self::LIST_CSS, $css);
    }

    /**
     * Get category label position
     *
     * @return string
     */
    public function getListPosition()
    {
        return $this->_getData(self::LIST_POSITION);
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setListPosition($position)
    {
        return $this->setData(self::LIST_POSITION, $position);
    }

    /**
     * Get category label position on grid
     *
     * @return string
     */
    public function getListPositionGrid()
    {
        return $this->_getData(self::LIST_POSITION_GRID);
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setListPositionGrid($position)
    {
        return $this->setData(self::LIST_POSITION_GRID, $position);
    }

    /**
     * Get Condition Serialized
     *
     * @return string
     */
    public function getConditionsSerialized()
    {
        return $this->_getData('conditions_serialized');
    }

    /**
     * @param string $condition
     *
     * @return $this
     */
    public function setConditionsSerialized($condition)
    {
        return $this->setData('conditions_serialized', $condition);
    }

    /**
     * Is apply bestseller for rule bale
     *
     * @return int
     */
    public function getBestSeller()
    {
        return $this->_getData('bestseller');
    }

    /**
     * @param int $isBestSeller
     *
     * @return $this
     */
    public function setBestSeller($isBestSeller)
    {
        return $this->setData('bestseller', $isBestSeller);
    }

    /**
     * Get limit product applied
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->_getData('limit');
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    public function setLimit($number)
    {
        return $this->setData('limit', $number);
    }

    /**
     * Get Label from date
     *
     * @return string
     */
    public function getFromDate()
    {
        return $this->_getData('from_date');
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setFromDate($date)
    {
        return $this->setData('from_date', $date);
    }

    /**
     * Get Label to date
     *
     * @return string
     */
    public function getToDate()
    {
        return $this->_getData('to_date');
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setToDate($date)
    {
        return $this->setData('to_date', $date);
    }

    /**
     * Is stop process
     *
     * @return int
     */
    public function getStopProcess()
    {
        return $this->_getData('stop_process');
    }

    /**
     * @param int $isStop
     *
     * @return $this
     */
    public function setStopProcess($isStop)
    {
        return $this->setData('stop_process', $isStop);
    }

    /**
     * Get Label create at date
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_getData('updated_at');
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setUpdatedAt($date)
    {
        return $this->setData('updated_at', $date);
    }

    /**
     * Get Label update at date
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData('created_at');
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setCreatedAt($date)
    {
        return $this->setData('created_at', $date);
    }
}
