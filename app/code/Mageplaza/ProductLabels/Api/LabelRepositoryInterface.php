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

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\ProductLabels\Api\Data\LabelInterface;

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
     * @return LabelInterface
     * @throws NoSuchEntityException
     */
    public function getById($ruleId);

    /**
     * Delete label rule by id
     *
     * @param int $ruleId
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function deleteById($ruleId);

    /**
     * Create/update label rule
     *
     * @param LabelInterface $label
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function add($label);

    /**
     * Update label rule
     *
     * @param LabelInterface $label
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function update($label);

    /**
     * Find label rules by given SearchCriteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return \Mageplaza\ProductLabels\Api\Data\LabelSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null);
}
