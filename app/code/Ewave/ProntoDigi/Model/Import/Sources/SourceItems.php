<?php

namespace Ewave\ProntoDigi\Model\Import\Sources;

use Magento\Catalog\Api\Data\ProductInterface;
use Ewave\ProntoDigi\ProntoApi\Constants\InventoryGetRequest;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Ewave\AI\Model\Logger\Logger;
use Magento\InventoryIndexer\Indexer\SourceItem\SourceItemIndexer;
use Magento\InventoryIndexer\Indexer\SourceItem\GetSourceItemIds;
use Magento\Inventory\Model\SourceItem;
use Magento\InventoryApi\Api\Data\SourceItemInterface;

/**
 * Class SourceItems
 * @package Ewave\ProntoDigi\Model\Import\Sources
 */
class SourceItems extends \Magento\Inventory\Model\ResourceModel\SourceItem
{
    /**
     * @var array
     */
    protected $notExistedSkus = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SourceItemIndexer
     */
    protected $sourceItemIndexer;

    /**
     * @var GetSourceItemIds
     */
    protected $getSourceItemIds;

    /**
     * SourceItems constructor.
     * @param Context $context
     * @param Logger $logger
     * @param SourceItemIndexer $sourceItemIndexer
     * @param GetSourceItemIds $getSourceItemIds
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        Logger $logger,
        SourceItemIndexer $sourceItemIndexer,
        GetSourceItemIds $getSourceItemIds,
        string $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->logger = $logger;
        $this->sourceItemIndexer = $sourceItemIndexer;
        $this->getSourceItemIds = $getSourceItemIds;
    }

    /**
     * @param SourceItem[] $sourceItems
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateData(array $sourceItems)
    {
        if (empty($sourceItems)) {
            return $this;
        }
        $existedSkus = $this->getExistedSkus();
        $dataToUpdate = [];
        foreach ($sourceItems as $key => $item) {
            $dataToUpdate[] = $item->getData();
            if (!isset($existedSkus[$item->getSku()])) {
                $this->notExistedSkus[] = $item->getSku();
                unset($sourceItems[$key]);
                unset($dataToUpdate[$key]);
            }
        }

        if (!empty($this->notExistedSkus)) {
            $this->logger->info(
                __(
                    'Skipped update for the SKU(s): %1',
                    implode(',', $this->notExistedSkus)
                )
            );
        }

        foreach (array_chunk($dataToUpdate, InventoryGetRequest::DEFAULT_BUNCH_SIZE) as $itemsBunch) {
            $this->getConnection()->insertOnDuplicate(
                $this->getMainTable(),
                $itemsBunch,
                [SourceItemInterface::QUANTITY, SourceItemInterface::STATUS]
            );
        }

        if (!empty($sourceItems)) {
            $sourceItemIds = $this->getSourceItemIds->execute($sourceItems);
            if (count($sourceItemIds)) {
                $this->sourceItemIndexer->executeList($sourceItemIds);
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    protected function getExistedSkus()
    {
        $select = $this->getConnection()
            ->select()
            ->from(
                ['p' => $this->getConnection()->getTableName('catalog_product_entity')],
                [ProductInterface::SKU, new \Zend_Db_Expr('1')]
            );

        return $this->getConnection()->fetchPairs($select);
    }
}
