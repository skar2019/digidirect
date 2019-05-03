<?php
namespace Ewave\AISales\Model\Import\CreditMemo\TypePreparer;

use Ewave\AISales\Model\Import\AbstractPreparer;
use Ewave\AISales\Model\Import\CreditMemo\Model\Processor;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;

class Items extends AbstractPreparer
{
    /**
     * @param array $creditmemo
     * @return array
     */
    public function prepareEntity(array &$creditmemo)
    {
        $creditmemo = $this->setItemsProductData(
            $creditmemo,
            CreditmemoItemInterface::ENTITY_ID,
            Processor::COL_ITEMS
        );

        $creditmemo = $this->setItemsIds(
            $creditmemo,
            CreditmemoItemInterface::ENTITY_ID,
            CreditmemoItemInterface::ORDER_ITEM_ID,
            CreditmemoInterface::ORDER_ID,
            Processor::COL_ITEMS
        );

        return $creditmemo;
    }
}
