<?php
namespace Ewave\AdvancedInventory\Model\ResourceModel\AbstractEntity\Relation\Stock;

use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Magento\CatalogInventory\Api\Data\StockInterfaceFactory;
use Magento\Framework\DataObject;

/**
 * Class SaveHandler
 */
class DeleteHandler extends AbstractHandler
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

        if ($stockId = $this->advancedInventoryStock->getStockIdByEntityId($entity->getData($linkField))) {
            try {
                $stock = $this->stockRepository->get($stockId);
                $this->stockRepository->delete($stock);
            } catch (\Exception $e) {
                //something went wrong
            }
        }

        return $entity;
    }
}
