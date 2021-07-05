<?php
namespace Digidirect\Faq\Model\ResourceModel\Tag;

use Magento\Store\Model\Store;

/**
 * Class Collection
 * @package Digidirect\Faq\Model\ResourceModel\Category
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_readConnection;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param null $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        $connection = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection);
        $this->_readConnection = $resourceConnection;
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Digidirect\Faq\Model\Tag', 'Digidirect\Faq\Model\ResourceModel\Tag');
    }
    
    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getReadConnection()
    {
        return $this->_readConnection->getConnection();
    }

    /**
     * @return $this
     */
    public function addFilterByUsingTags()
    {
        $this->getSelect()
            ->joinInner(['rel' => $this->getTable('digidirect_faq_tag_relation')], 'main_table.entity_id = rel.tag_id')
            ->group('main_table.entity_id');
        return $this;
    }
}
