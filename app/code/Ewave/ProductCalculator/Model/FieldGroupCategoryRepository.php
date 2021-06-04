<?php

namespace Ewave\ProductCalculator\Model;

use Ewave\ProductCalculator\Api\Data\FieldGroupCategoryInterface;
use Ewave\ProductCalculator\Api\FieldGroupCategoryRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class FieldGroupCategoryRepository implements FieldGroupCategoryRepositoryInterface
{
    /**
     * @var FieldGroupCategoryFactory
     */
    protected $fieldGroupCategoryFactory;

    /**
     * @var ResourceModel\FieldGroupCategory
     */
    protected $fieldGroupCategoryResource;

    /**
     * FieldGroupCategoryRepository constructor.
     * @param FieldGroupCategoryFactory $fieldGroupCategoryFactory
     * @param ResourceModel\FieldGroupCategory $fieldGroupCategoryResource
     */
    public function __construct(
        FieldGroupCategoryFactory $fieldGroupCategoryFactory,
        ResourceModel\FieldGroupCategory $fieldGroupCategoryResource
    ) {
        $this->fieldGroupCategoryFactory = $fieldGroupCategoryFactory;
        $this->fieldGroupCategoryResource = $fieldGroupCategoryResource;
    }

    /**
     * {@inheritdoc}
     */
    public function save(FieldGroupCategoryInterface $fieldGroupCategory)
    {
        try {
            $this->prepareData($fieldGroupCategory);
            $fieldGroupCategory->getResource()->save($fieldGroupCategory);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save product finder field group category: %1',
                $exception->getMessage()
            ));
        }

        return $fieldGroupCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($fieldGroupCategoryId)
    {
        $fieldGroupCategory = $this->fieldGroupCategoryFactory->create();
        $fieldGroupCategory->getResource()->load($fieldGroupCategory, $fieldGroupCategoryId);
        if (!$fieldGroupCategory->getId()) {
            throw new NoSuchEntityException(
                __('Field group category with id "%1" does not exist.', $fieldGroupCategoryId)
            );
        }

        return $fieldGroupCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(FieldGroupCategoryInterface $fieldGroupCategory)
    {
        try {
            $fieldGroupCategory->getResource()->delete($fieldGroupCategory);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete product finder field group category: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($fieldGroupCategoryId)
    {
        return $this->delete($this->getById($fieldGroupCategoryId));
    }

    /**
     * @param FieldGroupCategoryInterface $object
     * @return void
     */
    protected function prepareData(FieldGroupCategoryInterface $object)
    {
        if (!$object->getId()) {
            $object->unsetData('id');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus($ids, $status)
    {
        return $this->fieldGroupCategoryResource->updateStatus($ids, $status);
    }
}
