<?php

namespace Ewave\ProductCalculator\Model;

use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;
use Ewave\ProductCalculator\Api\Data\FieldInterface;
use Ewave\ProductCalculator\Model\ResourceModel\Field\CollectionFactory as FieldCollectionFactory;

class FieldGroup extends \Magento\Framework\Model\AbstractModel implements FieldGroupInterface
{
    /**
     * @var  FieldInterface[]|null
     */
    protected $relatedFields = null;

    /**
     * @var FieldCollectionFactory
     */
    private $fieldCollectionFactory;

    /**
     * FieldGroup constructor.
     * @param FieldCollectionFactory $fieldCollectionFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\FieldGroup $resource
     * @param ResourceModel\FieldGroup\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        FieldCollectionFactory $fieldCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\FieldGroup $resource,
        ResourceModel\FieldGroup\Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->fieldCollectionFactory = $fieldCollectionFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\FieldGroup::class);
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
     * {@inheritDoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
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
    public function isMandatory()
    {
        return (bool)$this->getData(self::IS_MANDATORY);
    }

    /**
     * {@inheritDoc}
     */
    public function setIsMandatory($isMandatory)
    {
        return $this->setData(self::IS_MANDATORY, $isMandatory);
    }

    /**
     * {@inheritdoc}
     */
    public function getMandatoryUserNotice()
    {
        return $this->getData(self::MANDATORY_USER_NOTICE);
    }

    /**
     * {@inheritDoc}
     */
    public function setMandatoryUserNotice($mandatoryUserNotice)
    {
        return $this->setData(self::MANDATORY_USER_NOTICE, $mandatoryUserNotice);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritDoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
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
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * {@inheritDoc}
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomInputName($customInputName)
    {
        return $this->setData(self::CUSTOM_INPUT_NAME, $customInputName);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomInputName()
    {
        return $this->getData(self::CUSTOM_INPUT_NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function setValueField($valueField)
    {
        return $this->setData(self::VALUE_FIELD, $valueField);
    }

    /**
     * {@inheritDoc}
     */
    public function getValueField()
    {
        return $this->getData(self::VALUE_FIELD) ?: self::ID;
    }

    /**
     * Get array of related field items
     *
     * @return FieldInterface[]|null
     */
    public function getRelatedFields()
    {
        if ($this->relatedFields === null && $this->getId()) {
            $fieldCollection = $this->fieldCollectionFactory->create();
            $this->relatedFields = $fieldCollection->getFieldGroupRelatedFields($this->getId());
        }

        return $this->relatedFields;
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        $this->getResource()->saveRelatedFields($this);
        return $this;
    }
}
