<?php
namespace Ewave\AISales\Model\Import\CreditMemo\TypePreparer;

use Ewave\AISales\Model\Import\AbstractPreparer;
use Ewave\AISales\Model\Import\CreditMemo\Model\Processor;
use Magento\Sales\Api\Data\CreditmemoInterface;

class Order extends AbstractPreparer
{
    /**
     * @param array $creditmemo
     * @return array
     */
    public function prepareEntity(array &$creditmemo)
    {
        $creditmemo = $this->setEntityOrderId(
            $creditmemo,
            CreditmemoInterface::ORDER_ID,
            Processor::COL_ORDER_INCREMENT_ID
        );

        return $creditmemo;
    }
}
