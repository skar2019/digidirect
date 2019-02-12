<?php

namespace Ewave\ProductCalculator\Api;

use Ewave\ProductCalculator\Api\Data\FieldGroupCategoryInterface;

/**
 * Interface FieldGroupCategoryRepositoryInterface
 * @package Ewave\ProductCalculator\Api
 */
interface FieldGroupCategoryRepositoryInterface
{
    /**
     * Save Field Group Category
     * @param FieldGroupCategoryInterface $fieldGroup
     * @return FieldGroupCategoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(FieldGroupCategoryInterface $fieldGroup);

    /**
     * Retrieve Field Group Category
     * @param string $fieldGroupId
     * @return FieldGroupCategoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($fieldGroupId);

    /**
     * Delete Field Group Category
     * @param FieldGroupCategoryInterface $fieldGroup
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(FieldGroupCategoryInterface $fieldGroup);

    /**
     * Delete Field Group Category by ID
     * @param string $fieldGroupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($fieldGroupId);

    /**
     * Update status
     *
     * @param [] $ids
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status);
}
