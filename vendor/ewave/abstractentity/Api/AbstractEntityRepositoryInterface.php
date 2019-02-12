<?php
namespace Ewave\AbstractEntity\Api;

use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface AbstractEntityRepositoryInterface
{
    /**
     * Save AbstractEntity
     * @param \Ewave\AbstractEntity\Api\Data\AbstractEntityInterface $abstractEntity
     * @return \Ewave\AbstractEntity\Api\Data\AbstractEntityInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(AbstractEntityInterface $abstractEntity);

    /**
     * Retrieve AbstractEntity
     * @param string $id
     * @param int $storeId
     * @return \Ewave\AbstractEntity\Api\Data\AbstractEntityInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id, $storeId = null);

    /**
     * Retrieve AbstractEntity matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param array|string|integer|\Magento\Framework\App\Config\Element $attributes
     * @param int|string $attributeSet
     * @return \Ewave\AbstractEntity\Api\Data\AbstractEntitySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $attributeSet = null, $attributes = null);

    /**
     * Delete AbstractEntity
     * @param \Ewave\AbstractEntity\Api\Data\AbstractEntityInterface $abstractEntity
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(AbstractEntityInterface $abstractEntity);

    /**
     * Delete AbstractEntity by ID
     * @param string $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
