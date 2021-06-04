<?php

namespace Ewave\ProductCalculator\Model\ResourceModel;

use Ewave\ProductCalculator\Api\Data\CalculatorFieldGroupCategoryInterface;
use Ewave\ProductCalculator\Api\Data\CalculatorInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\DB\Select;

/**
 * Class Calculator
 * @package Ewave\ProductCalculator\Model\ResourceModel
 */
class Calculator extends AbstractDb
{
    const CALCULATOR_TABLE = 'ewave_productcalculator_calculator';
    const CALCULATOR_FIELD_GROUP_CATEGORY_TABLE = 'ewave_productcalculator_calculator_field_group_category';

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    protected $entityManager;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        $connectionName = null
    ) {
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::CALCULATOR_TABLE, CalculatorInterface::ID);
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        $this->processAfterSaves($object);
        return $this;
    }

    /**
     * Load an object
     *
     * @param \Ewave\ProductCalculator\Model\Calculator|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $calculatorId = $this->getCalculatorId($object, $value, $field);
        if ($calculatorId) {
            $this->entityManager->load($object, $calculatorId);
        }
        $this->afterLoad($object);
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @param string $value
     * @param string|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getCalculatorId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(CalculatorInterface::class);

        if (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }

        $calculatorId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $calculatorId = count($result) ? $result[0] : false;
        }
        return $calculatorId;
    }

    /**
     * Save field group relations
     *
     * @param \Ewave\ProductCalculator\Model\Calculator $calculator
     * @return $this
     */
    public function saveFieldGroupCategories($calculator)
    {
        $calculatorId = $calculator->getId();
        $fieldGroupCategories = $calculator->getData('links/field_group_categories') ?? [];
        if ($calculatorId) {
            $connection = $this->getConnection();
            $fieldGroupCategoryIds = [];

            foreach ($fieldGroupCategories as $fieldGroup) {
                $fieldGroupCategoryIds[] = $fieldGroup['id'];
            }

            foreach ($fieldGroupCategoryIds as $fieldGroupId) {
                $connection->insertOnDuplicate(
                    $this->getTable(self::CALCULATOR_FIELD_GROUP_CATEGORY_TABLE),
                    [
                        CalculatorFieldGroupCategoryInterface::CALCULATOR_ID => $calculatorId,
                        CalculatorFieldGroupCategoryInterface::FIELD_GROUP_CATEGORY_ID => $fieldGroupId
                    ],
                    [CalculatorFieldGroupCategoryInterface::CALCULATOR_ID]
                );
            }

            if (empty($fieldGroupCategoryIds)) {
                $fieldGroupCategoryIds = [0];
            }

            $connection->delete(
                $this->getTable(self::CALCULATOR_FIELD_GROUP_CATEGORY_TABLE),
                [
                    CalculatorFieldGroupCategoryInterface::CALCULATOR_ID . ' = ?'    => $calculatorId,
                    CalculatorFieldGroupCategoryInterface::FIELD_GROUP_CATEGORY_ID
                    . ' NOT IN (?)'                                                  => $fieldGroupCategoryIds
                ]
            );
        }

        return $this;
    }

    /**
     * Update status by ids
     *
     * @param [] $id
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status)
    {
        $connection = $this->getConnection();
        return $connection->update(
            $this->getMainTable(),
            [CalculatorInterface::STATUS => $status],
            $connection->quoteInto(CalculatorInterface::ID . ' IN (?)', $ids)
        );
    }
}
