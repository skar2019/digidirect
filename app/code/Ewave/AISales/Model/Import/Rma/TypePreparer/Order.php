<?php
namespace Ewave\AISales\Model\Import\Rma\TypePreparer;

use Ewave\AISales\Model\Import\AbstractPreparer;
use Ewave\AISales\Model\Import\Rma\Model\Processor;
use Magento\Rma\Model\Rma;

class Order extends AbstractPreparer
{
    /**
     * @param array $rma
     * @return array
     */
    public function prepareEntity(array &$rma)
    {
        $rma = $this->setEntityOrderId(
            $rma,
            Rma::ORDER_ID,
            Processor::COL_ORDER_INCREMENT_ID
        );
        return $rma;
    }
}
