<?php
namespace Ewave\ProductCalculator\Model\ResourceModel\Calculator\Relation\Store;

use Ewave\ProductCalculator\Api\Data\CalculatorInterface;
use Ewave\ProductCalculator\Api\Data\CalculatorStoreInterface;
use Ewave\ProductCalculator\Model\ResourceModel\Calculator;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Calculator
     */
    protected $resourceCalculator;

    /**
     * SaveHandler constructor.
     * @param MetadataPool $metadataPool
     * @param Calculator $resourceCalculator
     */
    public function __construct(
        MetadataPool $metadataPool,
        Calculator $resourceCalculator
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceCalculator = $resourceCalculator;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(CalculatorInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldStores = $this->resourceCalculator->lookupStoreIds((int)$entity->getId());
        $newStores = (array)$entity->getStores();
        if (empty($newStores)) {
            $newStores = (array)$entity->getStoreId();
        }

        $table = $this->resourceCalculator->getTable(Calculator::CALCULATOR_STORE_TABLE);

        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = [
                CalculatorStoreInterface::CALCULATOR_ID . ' = ?' => (int)$entity->getData($linkField),
                CalculatorStoreInterface::STORE_ID . ' IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    CalculatorStoreInterface::CALCULATOR_ID => (int)$entity->getData($linkField),
                    CalculatorStoreInterface::STORE_ID => (int)$storeId
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
