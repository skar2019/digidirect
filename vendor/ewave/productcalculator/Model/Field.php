<?php

namespace Ewave\ProductCalculator\Model;

use Ewave\ProductCalculator\Api\Data\CalculatorInterface;
use Ewave\ProductCalculator\Api\Data\FieldInterface;
use Ewave\ProductCalculator\Model\ResourceModel\Field\Collection;

class Field extends \Magento\Framework\Model\AbstractModel implements FieldInterface
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
     * @var int
     */
    protected $calculatorId;

    /**
     * @param RuleFactory $ruleFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Ewave\ProductCalculator\Model\ResourceModel\Field $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        RuleFactory $ruleFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ewave\ProductCalculator\Model\ResourceModel\Field $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ewave\ProductCalculator\Model\ResourceModel\Field');
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
    public function getStatus()
    {
        return $this->getData(self::STATUS);
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
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * {@inheritDoc}
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
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
    public function setCssClass($cssClass)
    {
        return $this->setData(self::CSS_CLASS, $cssClass);
    }

    /**
     * {@inheritDoc}
     */
    public function getCssClass()
    {
        return $this->getData(self::CSS_CLASS);
    }

    /**
     * {@inheritDoc}
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * {@inheritDoc}
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
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
     * {@inheritDoc}
     */
    public function getCategoriesToShow()
    {
        return $this->getData(self::CATEGORIES_TO_SHOW);
    }

    /**
     * {@inheritDoc}
     */
    public function setCategoriesToShow($ids)
    {
        return $this->setData(self::CATEGORIES_TO_SHOW, $ids);
    }

    /**
     * Get input field calculator Id
     *
     * @return int|null
     */
    public function getCalculatorId()
    {
        if ($this->calculatorId === null) {
            $this->calculatorId = $this->_resource->getCalculatorId($this->getId());
        }

        return $this->calculatorId;
    }
}
