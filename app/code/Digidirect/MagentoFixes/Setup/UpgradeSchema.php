<?php
namespace Digidirect\MagentoFixes\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Trigger Factory
     *
     * @var \Magento\Framework\DB\Ddl\TriggerFactory
     */
    protected $triggerFactory;

    /**
     * UpgradeSchema constructor.
     *
     * @param \Magento\Framework\DB\Ddl\TriggerFactory $triggerFactory
     */
    public function __construct(
        \Magento\Framework\DB\Ddl\TriggerFactory $triggerFactory
    ) {
        $this->triggerFactory = $triggerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        // @codingStandardsIgnoreStart
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                $setup->getTable('indexer_state'),
                'is_need_invalid',
                [
                    'type'     => Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default'  => false,
                    'after'    => 'status',
                    'comment'  => 'Flag for invalidate reindex after finish'
                ]
            );
        }

        $setup->endSetup();
        // @codingStandardsIgnoreEnd
    }
}
