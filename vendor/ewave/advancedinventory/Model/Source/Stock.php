<?php
namespace Ewave\AdvancedInventory\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Stock extends AbstractSource
{
    /**
     * @var \Magento\CatalogInventory\Api\StockCriteriaInterface
     */
    protected $criteriaBuilder;

    /**
     * @var \Magento\CatalogInventory\Api\StockRepositoryInterface $stockRepository
     */
    protected $stockRepository;

    /**
     * ContractEntities constructor.
     * @param \Magento\CatalogInventory\Api\StockCriteriaInterface $criteriaBuilder
     * @param \Magento\CatalogInventory\Api\StockRepositoryInterface $stockRepository
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockCriteriaInterface $criteriaBuilder,
        \Magento\CatalogInventory\Api\StockRepositoryInterface $stockRepository
    ) {
        $this->criteriaBuilder = $criteriaBuilder;
        $this->stockRepository = $stockRepository;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $items = $this->stockRepository->getList($this->criteriaBuilder);
        $option = [];
        foreach ($items->getItems() as $item) {
            $option[] = [
                'value' => $item->getStockId(),
                'label' => $item->getStockName(),
            ];
        }
        return $option;
    }
}
