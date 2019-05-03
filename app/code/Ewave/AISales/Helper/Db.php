<?php

namespace Ewave\AISales\Helper;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Db
{
    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * BillingAddress constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param string $cols
     * @return bool
     */
    public function isEntityExist($table, $conditions, $cols = '(1)')
    {
        $tableName = $this->connection->getTableName($table);
        $select = $this->connection->select()
            ->from($tableName, $cols)
            ->limit(1);
        foreach ($conditions as $field => $value) {
            $field = $this->connection->quoteIdentifier($field);
            $select->where($tableName . '.' . $field . ' = ?', $value);
        }

        return (bool)$this->connection->fetchOne($select);
    }
}
