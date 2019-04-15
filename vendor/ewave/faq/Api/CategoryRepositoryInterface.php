<?php
namespace Ewave\Faq\Api;

/**
 * Faq category CRUD interface.
 */
interface CategoryRepositoryInterface extends AbstractFaqInterface
{
    /**
     * Retrieve abstract attributes which match a specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
