<?php
namespace Ewave\AISales\Model\Import\Sales;

use Ewave\AISales\Model\Import\AbstractValidator;
use Magento\Catalog\Api\Data\ProductInterface;

abstract class Validator extends AbstractValidator
{
    const SALES_ORDER_ITEM1 = 'sales_order_item';
    const INCREMENT_ID = 'increment_id';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    const SALES_ORDER = 'sales_order';
    const CATALOG_PRODUCT_ENTITY = 'catalog_product_entity';
    const SALES_ORDER_ITEM = 'sales_order_item';
    const ITEMS = 'items';
    const PRODUCT_SKU = 'product_sku';

    /**
     * @param array $item
     * @return string
     */
    protected function checkOrderItemRelationExist($item)
    {
        if (empty($item[ProductInterface::SKU])) {
            return true;
        }
        $isOrderItemExist = $this->dbHelper->isEntityExist(
            self::SALES_ORDER_ITEM,
            [ProductInterface::SKU => $item[ProductInterface::SKU]]
        );

        if (!$isOrderItemExist) {
            $this->messages[] = __('Order Item with sku = "%1" does not exist', $item[ProductInterface::SKU]);

            return false;
        }

        return true;
    }

    /**
     * @param array $item
     * @return bool
     */
    protected function checkOrderRelationExist($item)
    {
        if (empty($item[self::ORDER_INCREMENT_ID])) {
            return true;
        }
        $isOrderExist = $this->dbHelper->isEntityExist(
            self::SALES_ORDER,
            [self::INCREMENT_ID => $item[self::ORDER_INCREMENT_ID]]
        );
        if (!$isOrderExist) {
            $this->messages[] = __('Order with increment_id = "%1" does not exist', $item[self::ORDER_INCREMENT_ID]);

            return false;
        }

        return true;
    }

    /**
     * @param array $item
     * @return bool
     */
    protected function checkProductRelationExist($item)
    {
        $sku = $item[self::PRODUCT_SKU] ?? $item[ProductInterface::SKU] ?? null;
        if (empty($sku)) {
            return true;
        }
        $isProductExist = $this->dbHelper->isEntityExist(
            self::CATALOG_PRODUCT_ENTITY,
            [ProductInterface::SKU => $sku]
        );
        if (!$isProductExist) {
            $this->messages[] = __('Product with sku = %1 does not exist', $item[ProductInterface::SKU]);

            return false;
        }

        return true;
    }
}
