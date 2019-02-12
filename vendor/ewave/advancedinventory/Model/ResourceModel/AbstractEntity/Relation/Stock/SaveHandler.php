<?php
namespace Ewave\AdvancedInventory\Model\ResourceModel\AbstractEntity\Relation\Stock;

use Ewave\AdvancedInventory\Api\Data\AdvancedInventoryStockInterface;
use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Magento\CatalogInventory\Api\Data\StockInterfaceFactory;
use Magento\Framework\DataObject;

/**
 * Class SaveHandler
 */
class SaveHandler extends AbstractHandler
{
    /**
     * @param AbstractEntityInterface|DataObject $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        if (!in_array($entity->getAttributeSetId(), $this->configHelper->getAbstractEntities())) {
            return $entity;
        }

        $entityMetadata = $this->metadataPool->getMetadata(AbstractEntityInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $connection = $this->advancedInventoryStock->getConnection();

        $stockId = $this->advancedInventoryStock->getStockIdByEntityId($entity->getData($linkField));
        if (!$stockId) {
            /** @var \Magento\CatalogInventory\Api\Data\StockInterface $stock */
            $stock = $this->stockFactory->create();
            $stock->setStockName($entity->getData(AbstractEntityInterface::NAME));
            $this->stockRepository->save($stock);

            $connection->insertOnDuplicate(
                $this->advancedInventoryStock->getMainTable(),
                [
                    AdvancedInventoryStockInterface::STOCK_ID => $stock->getStockId(),
                    AdvancedInventoryStockInterface::ABSTRACT_ENTITY_ID => $entity->getId()
                ]
            );
        } else {
            $stock = $this->stockRepository->get($stockId);
            if ($stock->getStockName() != $entity->getData(AbstractEntityInterface::NAME)) {
                $stock->setStockName($entity->getData(AbstractEntityInterface::NAME));
                $this->stockRepository->save($stock);
            }
        }

        return $entity;
    }
}
