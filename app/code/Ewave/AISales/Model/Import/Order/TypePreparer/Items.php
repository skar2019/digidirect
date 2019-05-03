<?php
namespace Ewave\AISales\Model\Import\Order\TypePreparer;

use Ewave\AISales\Model\Import\AbstractPreparer;
use Ewave\AISales\Model\Import\Order\Model\Processor;
use Magento\Sales\Api\Data\OrderItemInterface;

class Items extends AbstractPreparer
{
    /**
     * @param array $order
     * @return array
     */
    public function prepareEntity(array &$order)
    {
        $order = $this->setItemsProductData(
            $order,
            OrderItemInterface::ITEM_ID,
            Processor::COL_ITEMS
        );

        return $order;
    }
}
