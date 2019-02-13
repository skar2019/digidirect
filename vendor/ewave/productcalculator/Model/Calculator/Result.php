<?php

namespace Ewave\ProductCalculator\Model\Calculator;

use Ewave\ProductCalculator\Api\FieldRepositoryInterface;
use Ewave\ProductCalculator\Model\Field;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Result
 */
class Result
{
    /**
     * @var []
     */
    protected $categoriesHash;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;

    /**
     * @var FieldRepositoryInterface
     */
    protected $fieldRepository;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param FieldRepositoryInterface $fieldRepository
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        FieldRepositoryInterface $fieldRepository
    ) {
        $this->productCollectionFactory = $collectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->catalogConfig = $catalogConfig;
        $this->sqlBuilder = $sqlBuilder;
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * Calculate product IDs based on provided calculator and user input data
     *
     * @param \Ewave\ProductCalculator\Model\Calculator $calculator
     * @param array $inputData
     * @return array
     */
    public function calculate($calculator, $inputData)
    {
        $productIds = [];

        $calculatorFilterIds = $this->getProductIdsByConditions($calculator->getRule()->getConditions());

        $initialIds = $calculatorFilterIds;
        foreach ($inputData as $fieldIdsGroup) {
            $fieldGroupFilterIds = [];
            foreach ($fieldIdsGroup as $fieldId) {
                try {
                    /** @var Field $field */
                    $field = $this->fieldRepository->getById($fieldId);
                } catch (NoSuchEntityException $e) {
                    continue;
                }

                $fieldFilterIds = $this->getProductIdsByConditions(
                    $field->getRule()->getConditions(),
                    $initialIds
                );
                $fieldGroupFilterIds = array_unique(array_merge($fieldGroupFilterIds, $fieldFilterIds));
            }
            $productIds = array_intersect($initialIds, $fieldGroupFilterIds);
            $initialIds = $productIds;
        }

        return $productIds;
    }

    /**
     * Get Categories To Show Ids based on $inputData
     *
     * @param array $inputData
     * @return array
     */
    public function getCategoriesToShowIds($inputData)
    {
        $hashKey = md5(serialize($inputData));
        if (!isset($this->categoriesHash[$hashKey])) {
            $this->categoriesHash[$hashKey] = [];
            foreach ($inputData as $fieldIdsGroup) {
                foreach ($fieldIdsGroup as $fieldId) {
                    try {
                        /** @var Field $field */
                        $field = $this->fieldRepository->getById($fieldId);
                    } catch (NoSuchEntityException $e) {
                        continue;
                    }

                    $this->categoriesHash[$hashKey] = array_merge(
                        $this->categoriesHash[$hashKey],
                        $field->getCategoriesToShow()
                    );
                }
            }
        }

        return $this->categoriesHash[$hashKey];
    }

    /**
     * Retrieve product IDs matching provided conditions
     *
     * @param \Magento\Rule\Model\Condition\Combine $conditions
     * @param array|null $initialIds
     * @return array
     */
    public function getProductIdsByConditions($conditions, $initialIds = null)
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        if ($initialIds !== null) {
            $collection->addIdFilter($initialIds);
        }

        $collection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addStoreFilter();

        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        return $collection->getAllIds();
    }
}
