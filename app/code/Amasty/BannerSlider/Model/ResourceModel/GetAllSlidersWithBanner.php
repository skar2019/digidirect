<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel;

use Amasty\BannerSlider\Api\Data\SliderInterface;
use Magento\Framework\App\ResourceConnection;

class GetAllSlidersWithBanner
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(int $bannerId): array
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()->from(
            $this->resourceConnection->getTableName(SliderInterface::RELATION_TABLE_NAME),
            [SliderInterface::SLIDER_ID]
        )->where(
            sprintf('%s = ?', SliderInterface::BANNER_ID),
            $bannerId
        );

        return $connection->fetchCol($select);
    }
}
