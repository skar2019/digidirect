<?php
namespace Ewave\Faq\Model\ResourceModel\Faq\Relation\Category;

use Ewave\Faq\Api\Data\FaqInterface;
use Ewave\Faq\Model\ResourceModel\Faq;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ResourceConnection;

/**
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $_metadataPool;

    /**
     * @var Faq
     */
    protected $_resourceFaq;

    /**
     * @var ResourceConnection
     */
    protected $_readConnection;

    /**
     * SaveHandler constructor.
     * @param MetadataPool $metadataPool
     * @param Faq $resourceFaq
     * @param ResourceConnection $readConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        Faq $resourceFaq,
        ResourceConnection $readConnection
    ) {
        $this->_metadataPool = $metadataPool;
        $this->_resourceFaq = $resourceFaq;
        $this->_readConnection = $readConnection;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     * 
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->_metadataPool->getMetadata(FaqInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $this->getReadConnection();

        $oldCategories = $this->_resourceFaq->lookupCategoryIds((int)$entity->getId());
        $newCategories = (array)$entity->getCategoreis();

        if (empty($newCategories)) {
            $newCategories = (array)$entity->getCategoryId();
        }

        $table = $this->_resourceFaq->getTable('ewave_faq_category_relation');

        $delete = array_diff($oldCategories, $newCategories);

        if ($delete) {
            $where = [
                'faq_id = ?' => (int)$entity->getData($linkField),
                'category_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newCategories, $oldCategories);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'faq_id' => (int)$entity->getData($linkField),
                    'category_id' => (int)$storeId
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getReadConnection()
    {
        return $this->_readConnection->getConnection();
    }
}
