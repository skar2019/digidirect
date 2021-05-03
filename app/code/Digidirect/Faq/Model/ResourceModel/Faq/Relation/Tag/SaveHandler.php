<?php
namespace Digidirect\Faq\Model\ResourceModel\Faq\Relation\Tag;

use Digidirect\Faq\Api\Data\FaqInterface;
use Digidirect\Faq\Model\ResourceModel\Faq;
use Digidirect\Faq\Model\ResourceModel\Tag;
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
     * @var Tag
     */
    protected $_resourceTag;

    /**
     * @var ResourceConnection
     */
    protected $_readConnection;

    /**
     * SaveHandler constructor.
     * @param MetadataPool $metadataPool
     * @param Faq $resourceFaq
     * @param Tag $resourceTag
     * @param ResourceConnection $readConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        Faq $resourceFaq,
        Tag $resourceTag,
        ResourceConnection $readConnection
    ) {
        $this->_metadataPool = $metadataPool;
        $this->_resourceFaq = $resourceFaq;
        $this->_resourceTag = $resourceTag;
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

        $oldTags = $this->_resourceFaq->lookupTags((int)$entity->getId());
        $newTags = explode(',', $entity->getTags());
        $newTags = array_map('trim', $newTags);
        $existsTags = $this->_resourceTag->getTagsByName($newTags);
        $needCreateTags = array_diff($newTags, $existsTags);
        if (!empty($needCreateTags)) {
            $table = $this->_resourceFaq->getTable('digidirect_faq_tag');
            $data = [];
            foreach ($needCreateTags as $newTag) {
                $newTag = trim($newTag);
                if (!empty($newTag)) {
                    $data[] = [
                        'title' => $newTag,
                    ];
                }

            }
            if (!empty($data)) {
                $connection->insertMultiple($table, $data);
            }
            $existsTags = $this->_resourceTag->getTagsByName($newTags);
        }
        $table = $this->_resourceFaq->getTable('digidirect_faq_tag_relation');
        $delete = array_keys(array_diff($oldTags, $newTags));
        if ($delete) {
            $where = [
                'faq_id = ?' => (int)$entity->getData($linkField),
                'tag_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }
        $insert = array_keys(array_diff($existsTags, $oldTags));
        if ($insert) {
            $data = [];
            foreach ($insert as $tagId) {
                $data[] = [
                    'faq_id' => (int)$entity->getData($linkField),
                    'tag_id' => (int)$tagId
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
