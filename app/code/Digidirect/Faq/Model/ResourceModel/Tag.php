<?php
namespace Digidirect\Faq\Model\ResourceModel;

use Digidirect\Faq\Model\Tag as FaqTag;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\EntityManager\EntityManager;

/**
 * Class Tag
 * @package Digidirect\Faq\Model\ResourceModel
 */
class Tag extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Tag constructor.
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('digidirect_faq_tag', 'entity_id');
    }

    /**
     * @param array $tags
     * @return array
     */
    public function getTagsByName(array $tags)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['tags' => $this->getTable('digidirect_faq_tag')]
        )->where('tags.title in (?)', $tags);
        return $connection->fetchPairs($select);
    }
}
