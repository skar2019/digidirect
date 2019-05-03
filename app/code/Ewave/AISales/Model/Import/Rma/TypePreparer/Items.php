<?php
namespace Ewave\AISales\Model\Import\Rma\TypePreparer;

use Ewave\AISales\Model\Import\AbstractPreparer;
use Ewave\AISales\Model\Import\Rma\Model\Processor;
use Magento\Rma\Model\Rma;
use Magento\Rma\Model\Item;
use Magento\Catalog\Api\Data\ProductInterface;

class Items extends AbstractPreparer
{
    const ITEM_PRODUCT_SKU = 'product_sku';
    const ITEM_PRODUCT_ADMIN_SKU = 'product_admin_sku';

    /**
     * @param array $rma
     * @return array
     */
    public function prepareEntity(array &$rma)
    {
        $rma = $this->setItemsProductData(
            $rma,
            Item::ENTITY_ID,
            Processor::COL_ITEMS
        );

        $rma = $this->setItemsIds(
            $rma,
            Item::ENTITY_ID,
            Item::ORDER_ITEM_ID,
            Rma::ORDER_ID,
            Processor::COL_ITEMS
        );

        return $rma;
    }

    /**
     * @param array $entity
     * @param string $itemIdKey
     * @param string $colItems
     * @return array
     */
    protected function setItemsProductData($entity, $itemIdKey, $colItems)
    {
        if (!isset($entity[$colItems])) {
            return $entity;
        }
        foreach ($entity[$colItems] as &$item) {
            if (isset($item[$itemIdKey])) {
                continue;
            }

            $item[ProductInterface::SKU] = $item[self::ITEM_PRODUCT_SKU];
            if (!isset($item[self::ITEM_PRODUCT_ADMIN_SKU])) {
                $item[self::ITEM_PRODUCT_ADMIN_SKU] = $item[self::ITEM_PRODUCT_SKU];
            }
        }
        return $entity;
    }
}
