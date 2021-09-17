<?php

namespace Amasty\BannerSlider\Setup;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;
use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Model\Analytics\Temp\TempEntity;
use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var TempEntity
     */
    private $tempEntity;

    public function __construct(
        AppResource $resource,
        TempEntity $tempEntity
    ) {
        $this->connection = $resource->getConnection();
        $this->tempEntity = $tempEntity;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tablesToDrop = [
            BannerInterface::STATIC_TABLE_NAME,
            BannerInterface::DYNAMIC_TABLE_NAME,
            SliderInterface::STATIC_TABLE_NAME,
            SliderInterface::DYNAMIC_TABLE_NAME,
            SliderInterface::RELATION_TABLE_NAME,
            AnalyticInterface::MAIN_TABLE,
            $this->tempEntity->getTableName(TempEntity::CLICK_TYPE),
            $this->tempEntity->getTableName(TempEntity::VIEW_TYPE)
        ];

        foreach ($tablesToDrop as $table) {
            $this->connection->dropTable(
                $installer->getTable($table)
            );
        }

        $installer->endSetup();
    }
}
