<?php
namespace Digidirect\Faq\Model\Config\Source;

use Digidirect\Faq\Api\CategoryRepositoryInterface;
use Digidirect\Faq\Model\Category;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class FaqCategory
 *
 * @package Digidirect\Faq\Model\Config\Source
 */
class FaqCategory implements ArrayInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $faqCategoryRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * FaqCategory constructor.
     *
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->faqCategoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /**
         * @var $categories Category[]
         */
        $categories = $this->faqCategoryRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        $options = [];
        foreach ($categories as $category) {
            $options[] = [
                'label' => $category->getTitle(),
                'value' => $category->getId(),
            ];
        }

        return $options;
    }
}
