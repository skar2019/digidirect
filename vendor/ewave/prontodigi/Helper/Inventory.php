<?php

namespace Ewave\ProntoDigi\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory as SourceItemCollectionFactory;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory as SourceCollectionFactory;

/**
 * Class Inventory
 * @package Ewave\ProntoDigi\Helper
 */
class Inventory extends AbstractHelper
{
    /**
     * @var SourceCollectionFactory
     */
    protected $sourceCollectionFactory;

    /**
     * @var SourceItemCollectionFactory
     */
    protected $sourceItemCollectionFactory;

    /**
     * @var null|array
     */
    protected $sources;

    /**
     * Inventory constructor.
     * @param Context $context
     * @param SourceItemCollectionFactory $sourceItemCollectionFactory
     * @param SourceCollectionFactory $sourceCollectionFactory
     */
    public function __construct(
        Context $context,
        SourceItemCollectionFactory $sourceItemCollectionFactory,
        SourceCollectionFactory $sourceCollectionFactory
    ) {
        parent::__construct($context);
        $this->sourceItemCollectionFactory = $sourceItemCollectionFactory;
        $this->sourceCollectionFactory = $sourceCollectionFactory;
    }

    /**
     * @param array $skus
     * @return array
     */
    public function getSourceItemsData($skus = [])
    {
        $itemsBySkus = [];
        $sourceItems = $this->getSourceItems($skus);
        $sourcesBySourceCode = $this->getSourcesBySourceItems($sourceItems);

        foreach ($sourceItems as $sourceItem) {
            $sku = $sourceItem[SourceItemInterface::SKU];
            $source = $sourcesBySourceCode[$sourceItem[SourceInterface::SOURCE_CODE]];
            $itemsBySkus[$sku][$source[SourceInterface::SOURCE_CODE]] = $sourceItem;
        }

        return $itemsBySkus;
    }

    /**
     * Get all sources by source items codes.
     * @param array $sourceItems
     * @return array
     */
    public function getSourcesBySourceItems(array $sourceItems)
    {
        $sourcesBySourceCodes = [];
        $allSources = $this->getSources();
        foreach ($sourceItems as $sourceItem) {
            $sourcesBySourceCodes[$sourceItem[SourceItemInterface::SOURCE_CODE]] =
                $allSources[$sourceItem[SourceItemInterface::SOURCE_CODE]];
        }

        return $sourcesBySourceCodes;
    }

    /**
     * @return array
     */
    public function getSources()
    {
        if ($this->sources == null) {
            $collection = $this->sourceCollectionFactory->create();
            $select = $collection->getSelect();
            $this->sources = $collection->getConnection()->fetchAssoc($select);
        }
        return $this->sources;
    }

    /**
     * @param array $skus
     * @return array
     */
    public function getSourceItems($skus = [])
    {
        $collection = $this->sourceItemCollectionFactory->create();

        if (!empty($skus)) {
            $collection->addFieldToFilter(SourceItemInterface::SKU, ['in' => array_keys($skus)]);
        }
        $select = $collection->getSelect();
        return $collection->getConnection()->fetchAll($select);
    }
}
