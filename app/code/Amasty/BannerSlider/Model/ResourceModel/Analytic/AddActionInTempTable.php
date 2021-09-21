<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel\Analytic;

use Amasty\BannerSlider\Model\Analytics\Temp\TempEntity;
use Exception;
use Magento\Framework\App\ResourceConnection;
use Zend_Db_Exception;

class AddActionInTempTable
{
    /**
     * @var TempEntity
     */
    private $tempEntity;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        TempEntity $tempEntity,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->tempEntity = $tempEntity;
    }

    /**
     * @param string $type
     * @param array $data
     * @return void
     * @throws Zend_Db_Exception
     * @throws Exception
     */
    public function execute(string $type, array $data): void
    {
        $this->resourceConnection->getConnection()->insertMultiple(
            $this->resourceConnection->getTableName($this->tempEntity->getTableName($type)),
            $data
        );
    }
}
