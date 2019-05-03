<?php
namespace Ewave\AISales\Model\Import\Rma\Model;

use Magento\Framework\DB\Adapter\AdapterInterface;

class Grid extends \Magento\Sales\Model\ResourceModel\Grid
{
    /**
     * Adds new RMAs to the grid.
     *
     * @return \Zend_Db_Statement_Interface
     */
    public function refreshBySchedule()
    {
        $select = $this->getGridOriginSelect();
        return $this->getConnection()->query(
            $this->getConnection()
                ->insertFromSelect(
                    $select,
                    $this->getTable($this->gridTableName),
                    array_keys($this->columns),
                    AdapterInterface::INSERT_ON_DUPLICATE
                )
        );
    }
}
