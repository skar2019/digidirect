<?php
namespace Ewave\AISales\Model\Import\Rma\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Rma\Model\Processor;
use Ewave\AISales\Model\Import\Rma\TypePreparer\Items as ItemsPreparer;
use Ewave\AISales\Model\Import\Rma\Model\Eav;
use Magento\Rma\Model\Item;
use Magento\Framework\App\ResourceConnection;

class Items extends AbstractRelationPreparer
{
    const TABLE = 'magento_rma_item_entity';

    /**
     * @var Eav
     */
    protected $eav;

    /**
     * RelationPreparerAbstract constructor.
     * @param ResourceConnection $resourceConnection
     * @param Eav $eav
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Eav $eav
    ) {
        parent::__construct($resourceConnection);
        $this->eav = $eav;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return self::TABLE;
    }

    /**
     * @param int $rmaId
     * @param array $rmaData
     * @return array
     */
    public function getRow($rmaId, array $rmaData)
    {
        $data = $this->getItemsData(
            $rmaData,
            $rmaId,
            Item::RMA_ENTITY_ID,
            Processor::COL_ITEMS
        );

        foreach ($data as $table => $insertData) {
            $this->connection->insertMultiple(
                $this->connection->getTableName($table),
                $insertData
            );
        }

        $data = [];
        foreach ($this->getUpdatedRow($rmaId, $rmaData) as $table => $insertData) {
            if ($table != $this->getTable()) {
                $data[$table] = $insertData;
            }
        }

        return $data;
    }

    /**
     * @param int $rmaId
     * @param array $rmaData
     * @return array
     */
    public function getUpdatedRow($rmaId, array $rmaData)
    {
        $data = $this->getEntityItemsData(
            $rmaData,
            $rmaId,
            Item::RMA_ENTITY_ID,
            Item::ENTITY_ID,
            Processor::COL_ITEMS,
            ItemsPreparer::ITEM_PRODUCT_SKU
        );
        return array_merge_recursive($data, $this->getEavAttributesRow($rmaData));
    }

    /**
     * @param array $rmaData
     * @return array
     */
    protected function getEavAttributesRow(array $rmaData)
    {
        $data = [];
        $items = $rmaData[Processor::COL_ITEMS] ?? [];
        $attributes = $this->eav->getRmaItemAttributes();
        foreach ($items as $item) {
            if (!isset($item[Item::ENTITY_ID])) {
                continue;
            }

            foreach ($attributes as $attributeCode => $attribute) {
                if (isset($item[$attributeCode]) && !empty($item[$attributeCode])) {
                    foreach ($attribute['values'] as $value) {
                        if (in_array($item[$attributeCode], $value)) {
                            $item[$attributeCode] = $value['option_id'];
                        }
                    }

                    $data[$attribute['entity_table'] . '_' . $attribute['backend_type']][] = [
                        'attribute_id' => $attribute['attribute_id'],
                        'entity_id' => $item[Item::ENTITY_ID],
                        'value' => $item[$attributeCode],
                    ];
                }
            }
        }
        return $data;
    }
}
