<?php

namespace Ewave\ProductCalculator\Model;

use Ewave\ProductCalculator\Api\Data\FieldGroupCategoryInterface;
use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroup\CollectionFactory as FieldGroupCollectionFactory;

/**
 * Class FieldGroupCategory
 * @package Ewave\ProductCalculator\Model
 */
class FieldGroupCategory extends \Magento\Framework\Model\AbstractModel implements FieldGroupCategoryInterface
{
    /**
     * @var FieldGroupCollectionFactory
     */
    protected $fieldGroupCollectionFactory;

    /**
     * @var FieldGroupInterface[]|null
     */
    protected $fieldGroups = null;

    /**
     * FieldGroupCategory constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param FieldGroupCollectionFactory $fieldGroupCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        FieldGroupCollectionFactory $fieldGroupCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->fieldGroupCollectionFactory = $fieldGroupCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ewave\ProductCalculator\Model\ResourceModel\FieldGroupCategory');
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
    public function getFieldGroups()
    {
        if ($this->fieldGroups === null && $this->getId()) {
            $fieldGroupCollection = $this->fieldGroupCollectionFactory->create();
            $this->fieldGroups = $fieldGroupCollection->getCategoryFieldGroups($this->getId());
        }

        return $this->fieldGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        $this->getResource()->saveFieldGroups($this);
        return $this;
    }
}
