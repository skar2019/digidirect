<?php

namespace Ewave\ProntoDigi\Helper;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryConfigurationApi\Model\GetAllowedProductTypesForSourceItemManagementInterface;

/**
 * Class Inventory
 * @package Ewave\ProntoDigi\Helper
 */
class Inventory extends AbstractHelper
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SourceItemRepositoryInterface
     */
    protected $sourceItemRepository;

    /**
     * @var GetAllowedProductTypesForSourceItemManagementInterface
     */
    protected $allowedProductTypesForSourceItemManagement;

    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * Inventory constructor.
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param GetAllowedProductTypesForSourceItemManagementInterface $allowedProductTypesForSourceItemManagement
     * @param SourceRepositoryInterface $sourceRepository
     * @param Context $context
     */
    public function __construct(
        SearchCriteriaBuilder $criteriaBuilder,
        SourceItemRepositoryInterface $sourceItemRepository,
        GetAllowedProductTypesForSourceItemManagementInterface $allowedProductTypesForSourceItemManagement,
        SourceRepositoryInterface $sourceRepository,
        Context $context
    ) {
        $this->searchCriteriaBuilder = $criteriaBuilder;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->allowedProductTypesForSourceItemManagement = $allowedProductTypesForSourceItemManagement;
        $this->sourceRepository = $sourceRepository;
        parent::__construct($context);
    }

    /**
     * @param array $skus
     * @return array
     */
    public function getSourceItemsData($skus = [])
    {
        $itemsBySkus = [];
        $searchCriteria = $this->searchCriteriaBuilder;
        if (!empty($skus)) {
            $searchCriteria = $searchCriteria->addFilter(
                SourceItemInterface::SKU,
                $skus, 'in'
            );
        }
        $searchCriteria = $searchCriteria->create();

        $sourceItems = $this->sourceItemRepository->getList($searchCriteria)->getItems();
        $sourcesBySourceCode = $this->getSourcesBySourceItems($sourceItems);

        foreach ($sourceItems as $sourceItem) {
            $sku = $sourceItem->getSku();
            $source = $sourcesBySourceCode[$sourceItem->getSourceCode()];
            $itemsBySkus[$sku][$source->getSourceCode()] = $sourceItem;
        }

        return $itemsBySkus;
    }

    /**
     * Get all sources by source items codes.
     *
     * @param SourceItemInterface[] $sourceItems
     * @return array
     */
    public function getSourcesBySourceItems(array $sourceItems)
    {
        $newSourceCodes = $sourcesBySourceCodes = [];

        foreach ($sourceItems as $sourceItem) {
            $newSourceCodes[$sourceItem->getSourceCode()] = $sourceItem->getSourceCode();
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceInterface::SOURCE_CODE, array_keys($newSourceCodes), 'in')
            ->create();
        $sources = $this->sourceRepository->getList($searchCriteria)->getItems();

        foreach ($sources as $source) {
            $sourcesBySourceCodes[$source->getSourceCode()] = $source;
        }

        return $sourcesBySourceCodes;
    }

    /**
     * @return SourceInterface[]
     */
    public function getSources()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $sources = $this->sourceRepository->getList($searchCriteria)->getItems();
        return $sources;
    }
}
