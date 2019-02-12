<?php
namespace Ewave\Navigation\Model\Config\Source;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Ewave\Navigation\Api\SetRepositoryInterface;

class Set implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var SetRepositoryInterface
     */
    protected $setRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Set constructor.
     * @param SetRepositoryInterface $setRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        SetRepositoryInterface $setRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->setRepository = $setRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Get all menu items sets using sets API
     *
     * @return []
     */
    public function toOptionArray()
    {
        $searchResults = $this->setRepository->getList($this->searchCriteriaBuilder->create());
        $items = $searchResults->getItems();
        $optionsArray = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                $optionsArray[] = [
                    'value' => $item['set_id'] ?? null,
                    'label' => $item['name'] ?? null
                ];
            }
        }
        return $optionsArray;
    }
}
