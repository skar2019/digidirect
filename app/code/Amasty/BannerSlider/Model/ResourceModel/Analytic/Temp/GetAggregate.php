<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel\Analytic\Temp;

use Amasty\BannerSlider\Model\Analytics\Temp\TempEntity;
use Exception;
use Magento\Framework\App\ResourceConnection;

class GetAggregate
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
     * @param int $bannerId
     * @param int $versionId
     * @param string $type
     * @return array|null
     * @throws Exception
     */
    public function execute(int $bannerId, int $versionId, string $type): ?array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            $this->resourceConnection->getTableName($this->tempEntity->getTableName($type)),
            [
                TempEntity::AGGREGATE_COUNTER => 'COUNT(*)',
                TempEntity::AGGREGATE_VERSION => sprintf('MAX(%s)', TempEntity::ID)
            ]
        )->where(
            sprintf('%s > ?', TempEntity::ID),
            $versionId
        )->having(
            sprintf('%s = ?', TempEntity::BANNER_ID),
            $bannerId
        )->group(
            TempEntity::BANNER_ID
        );

        $result = $connection->fetchRow($select);

        return $result ?: null;
    }
}
