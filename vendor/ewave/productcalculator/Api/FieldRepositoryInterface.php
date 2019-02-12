<?php

namespace Ewave\ProductCalculator\Api;

use Ewave\ProductCalculator\Api\Data\FieldInterface;

interface FieldRepositoryInterface
{
    /**
     * Save Field
     * @param FieldInterface $field
     * @return FieldInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(FieldInterface $field);

    /**
     * Retrieve Field
     * @param string $fieldId
     * @return FieldInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($fieldId);

    /**
     * Delete Field
     * @param FieldInterface $field
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(FieldInterface $field);

    /**
     * Delete Field by ID
     * @param string $fieldId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($fieldId);

    /**
     * Update items status
     *
     * @param [] $ids
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status);
}
