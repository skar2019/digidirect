<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Api;

/**
 * Interface LabelRepositoryInterface
 * @package Mageplaza\ProductLabels\Api\Data
 */
interface LabelRepositoryInterface
{
    /**
     * Get label rule by id
     *
     * @param int $ruleId
     *
     * @return \Mageplaza\ProductLabels\Api\Data\LabelInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($ruleId);

    /**
     * Delete label rule by id
     *
     * @param int $ruleId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($ruleId);

    /**
     * Create/update label rule
     *
     * @param \Mageplaza\ProductLabels\Api\Data\LabelInterface $label
     *
     * @return \Mageplaza\ProductLabels\Api\Data\LabelInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function add($label);

    /**
     * Update label rule
     *
     * @param \Mageplaza\ProductLabels\Api\Data\LabelInterface $label
     *
     * @return \Mageplaza\ProductLabels\Api\Data\LabelInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update($label);

    /**
     * Find label rules by given SearchCriteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Mageplaza\ProductLabels\Api\Data\LabelSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);
}
