<?php

namespace Ewave\ProductCalculator\Model;

use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;
use Ewave\ProductCalculator\Api\FieldGroupRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class FieldGroupRepository implements FieldGroupRepositoryInterface
{
    /**
     * @var FieldGroupFactory
     */
    protected $fieldGroupFactory;

    /**
     * @var ResourceModel\FieldGroup
     */
    protected $fieldGroupResource;

    /**
     * FieldGroupRepository constructor.
     * @param FieldGroupFactory $fieldGroupFactory
     * @param ResourceModel\FieldGroup $fieldGroupResource
     */
    public function __construct(
        FieldGroupFactory $fieldGroupFactory,
        \Ewave\ProductCalculator\Model\ResourceModel\FieldGroup $fieldGroupResource
    ) {
        $this->fieldGroupFactory = $fieldGroupFactory;
        $this->fieldGroupResource = $fieldGroupResource;
    }

    /**
     * {@inheritdoc}
     */
    public function save(FieldGroupInterface $fieldGroup)
    {
        try {
            $this->prepareData($fieldGroup);
            $fieldGroup->getResource()->save($fieldGroup);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save product finder field group: %1',
                $exception->getMessage()
            ));
        }
        return $fieldGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($fieldGroupId)
    {
        $fieldGroup = $this->fieldGroupFactory->create();
        $fieldGroup->getResource()->load($fieldGroup, $fieldGroupId);
        if (!$fieldGroup->getId()) {
            throw new NoSuchEntityException(__('Field group with id "%1" does not exist.', $fieldGroupId));
        }
        return $fieldGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(FieldGroupInterface $fieldGroup)
    {
        try {
            $fieldGroup->getResource()->delete($fieldGroup);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete product finder field: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($fieldGroupId)
    {
        return $this->delete($this->getById($fieldGroupId));
    }

    /**
     * @param FieldGroupInterface $object
     * @return void
     */
    protected function prepareData(FieldGroupInterface $object)
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
        return $this->fieldGroupResource->updateStatus($ids, $status);
    }
}
