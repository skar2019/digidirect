<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel\Analytic;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;
use Amasty\BannerSlider\Model\Analytics\Temp\TempEntity;
use Magento\Framework\App\ResourceConnection;

class Clear
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

    public function execute(): void
    {
        $connection = $this->resourceConnection->getConnection();

        $connection->truncateTable(
            $this->getTableName($this->tempEntity->getTableName(TempEntity::CLICK_TYPE))
        );
        $connection->truncateTable(
            $this->getTableName($this->tempEntity->getTableName(TempEntity::VIEW_TYPE))
        );
        $connection->update(
            $this->getTableName(AnalyticInterface::MAIN_TABLE),
            ['version_id' => 0]
        );
    }

    private function getTableName(string $tableName): string
    {
        return $this->resourceConnection->getTableName($tableName);
    }
}
