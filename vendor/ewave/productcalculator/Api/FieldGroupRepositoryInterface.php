<?php

namespace Ewave\ProductCalculator\Api;

use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;

interface FieldGroupRepositoryInterface
{
    /**
     * Save Field
     * @param FieldGroupInterface $fieldGroup
     * @return FieldGroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(FieldGroupInterface $fieldGroup);

    /**
     * Retrieve Field
     * @param string $fieldGroupId
     * @return FieldGroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($fieldGroupId);

    /**
     * Delete Field
     * @param FieldGroupInterface $fieldGroup
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(FieldGroupInterface $fieldGroup);

    /**
     * Delete Field by ID
     * @param string $fieldGroupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($fieldGroupId);

    /**
     * Update items status
     *
     * @param [] $ids
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status);
}
