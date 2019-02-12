<?php

namespace Ewave\ProductCalculator\Model;

use Ewave\ProductCalculator\Api\Data\CalculatorInterface;
use Ewave\ProductCalculator\Api\Data\FieldGroupCategoryInterface;
use Ewave\ProductCalculator\Model\ResourceModel\Calculator\Collection;
use Ewave\ProductCalculator\Model\Source\ResultLoadActions;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroupCategory\CollectionFactory
    as FieldGroupCategoryCollectionFactory;

/**
 * Class Calculator
 * @package Ewave\ProductCalculator\Model
 */
class Calculator extends \Magento\Framework\Model\AbstractModel implements CalculatorInterface
{
    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var FieldGroupCategoryInterface[]|null
     */
    protected $fieldGroupCategories = null;

    /**
     * @var FieldGroupCategoryCollectionFactory
     */
    protected $fieldGroupCategoryCollectionFactory;

    /**
     * Calculator constructor.
     * @param FieldGroupCategoryCollectionFactory $fieldGroupCategoryCollectionFactory
     * @param RuleFactory $ruleFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Calculator $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        FieldGroupCategoryCollectionFactory $fieldGroupCategoryCollectionFactory,
        RuleFactory $ruleFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ewave\ProductCalculator\Model\ResourceModel\Calculator $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->ruleFactory = $ruleFactory;
        $this->fieldGroupCategoryCollectionFactory = $fieldGroupCategoryCollectionFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ewave\ProductCalculator\Model\ResourceModel\Calculator');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonLabel()
    {
        return $this->getData(self::BUTTON_LABEL);
    }

    /**
     * {@inheritDoc}
     */
    public function setButtonLabel($buttonLabel)
    {
        return $this->setData(self::BUTTON_LABEL, $buttonLabel);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductsCount()
    {
        return $this->getData(self::PRODUCTS_COUNT);
    }

    /**
     * {@inheritDoc}
     */
    public function setProductsCount($productsCount)
    {
        return $this->setData(self::PRODUCTS_COUNT, $productsCount);
    }

    /**
     * Get products count for category
     *
     * @return int
     */
    public function getProductsCountForCategory()
    {
        return $this->getData(self::PRODUCTS_COUNT_FOR_CATEGORY);
    }

    /**
     * Set products count for category
     *
     * @param int $productsCount
     * @return CalculatorInterface
     */
    public function setProductsCountForCategory($productsCount)
    {
        return $this->setData(self::PRODUCTS_COUNT_FOR_CATEGORY, $productsCount);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return (int)$this->getData(self::STATUS);
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * {@inheritDoc}
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * {@inheritDoc}
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * {@inheritDoc}
     */
    public function getResultLoadAction()
    {
        return $this->getData(self::RESULT_LOAD_ACTION);
    }

    /**
     * {@inheritDoc}
     */
    public function isResultLoadActionRedirect()
    {
        return $this->getFlagCustomLoadUrl() && $this->getResultLoadAction() == ResultLoadActions::REDIRECT;
    }

    /**
     * {@inheritDoc}
     */
    public function setResultLoadAction($resultLoadAction)
    {
        return $this->setData(self::RESULT_LOAD_ACTION, $resultLoadAction);
    }

    /**
     * {@inheritDoc}
     */
    public function getResultLoadUrl()
    {
        return $this->getData(self::RESULT_LOAD_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function setResultLoadUrl($resultLoadUrl)
    {
        return $this->setData(self::RESULT_LOAD_URL, $resultLoadUrl);
    }

    /**
     * {@inheritDoc}
     */
    public function getFlagCustomLoadUrl()
    {
        return $this->getData(self::FLAG_CUSTOM_LOAD_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function setFlagCustomLoadUrl($flagCustomLoadUrl)
    {
        return $this->setData(self::FLAG_CUSTOM_LOAD_URL, $flagCustomLoadUrl);
    }

    /**
     * Get rule model for calculator
     *
     * @return Rule
     */
    public function getRule()
    {
        if (!$this->rule) {
            /** @var Rule $rule */
            $this->rule = $this->ruleFactory->create();
            if ($this->getId()) {
                $this->rule->getConditions()->loadArray(unserialize($this->getConditionsSerialized()));
            }
        }

        return $this->rule;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldGroupCategories()
    {
        if ($this->fieldGroupCategories === null && $this->getId()) {
            $fieldGroupCollection = $this->fieldGroupCategoryCollectionFactory->create();
            $this->fieldGroupCategories = $fieldGroupCollection->getCalculatorFieldGroupCategories($this->getId());
        }

        return $this->fieldGroupCategories;
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        $this->getResource()->saveFieldGroupCategories($this);
        return $this;
    }

    /**
     * @return int
     */
    public function getSeparateProductsByCategories()
    {
        return $this->getData(self::SEPARATE_PRODUCTS_BY_CATEGORIES);
    }

    /**
     * @param int $value
     * @return CalculatorInterface
     */
    public function setSeparateProductsByCategories($value)
    {
        return $this->setData(self::SEPARATE_PRODUCTS_BY_CATEGORIES, $value);
    }
}
