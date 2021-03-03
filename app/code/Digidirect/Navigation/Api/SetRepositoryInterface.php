<?php
namespace Digidirect\Navigation\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Navigation set CRUD interface.
 */
interface SetRepositoryInterface extends AbstractNavigationInterface
{
    /**
     * Save set.
     *
     * @param \Digidirect\Navigation\Api\Data\SetInterface $set
     * @return \Digidirect\Navigation\Api\Data\SetInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\SetInterface $set);

    /**
     * Retrieve set.
     *
     * @param int $setId
     * @return \Digidirect\Navigation\Api\Data\SetInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($setId);

    /**
     * Retrieve sets matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Digidirect\Navigation\Api\Data\SetSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete set.
     *
     * @param \Digidirect\Navigation\Api\Data\SetInterface $set
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\SetInterface $set);

    /**
     * Delete navigation set by ID.
     *
     * @param int $setId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($setId);
}
