<?php
namespace Ewave\AISales\Model\Import\Rma\Model;

use Magento\Rma\Model\Item;
use Magento\Eav\Api\Data\AttributeInterface as Attribute;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\ResourceConnection;

class Eav
{
    const TABLE_EAV_ENTITY_TYPE = 'eav_entity_type';
    const TABLE_EAV_ATTRIBUTE = 'eav_attribute';
    const TABLE_EAV_ATTRIBUTE_OPTION = 'eav_attribute_option';
    const TABLE_EAV_ATTRIBUTE_OPTION_VALUE = 'eav_attribute_option_value';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var []
     */
    protected $rmaItemAttributes;

    /**
     * @var string
     */
    protected $entityTypeCode;

    /**
     * @var array
     */
    protected $excludeBackedTypes = [];

    /**
     * RelationPreparerAbstract constructor.
     * @param ResourceConnection $resourceConnection
     * @param string $entityTypeCode
     * @param array $excludeBackedTypes
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        $entityTypeCode = Item::ENTITY,
        $excludeBackedTypes = [AbstractAttribute::TYPE_STATIC]
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->entityTypeCode = $entityTypeCode;
        $this->excludeBackedTypes = $excludeBackedTypes;
    }

    /**
     * @return array
     */
    public function getRmaItemAttributes()
    {
        if ($this->rmaItemAttributes === null) {
            $select = $this->connection->select()
                ->from(
                    ['attr' => $this->connection->getTableName(self::TABLE_EAV_ATTRIBUTE)],
                    [
                        Attribute::ATTRIBUTE_ID,
                        Attribute::ATTRIBUTE_CODE,
                        Attribute::BACKEND_TYPE,
                        Attribute::IS_REQUIRED
                    ]
                )
                ->joinInner(
                    ['type' => $this->connection->getTableName(self::TABLE_EAV_ENTITY_TYPE)],
                    'attr.entity_type_id = type.entity_type_id AND type.entity_type_code = :entityTypeCode',
                    ['entity_table']
                )
                ->where(Attribute::BACKEND_TYPE . ' NOT IN (:backendType)');

            $attributes = $this->connection->fetchAll($select, [
                'entityTypeCode' => $this->entityTypeCode,
                'backendType' => implode('", "', $this->excludeBackedTypes),
            ]);

            foreach ($attributes as $attribute) {
                $attribute['values'] = $this->getAttributeOptions($attribute[Attribute::ATTRIBUTE_ID]);
                $this->rmaItemAttributes[$attribute[Attribute::ATTRIBUTE_CODE]] = $attribute;
            }
        }
        return $this->rmaItemAttributes;
    }

    /**
     * @param int $attributeId
     * @return array
     */
    public function getAttributeOptions($attributeId)
    {
        $select = $this->connection->select()
            ->from(
                ['option' => $this->connection->getTableName(self::TABLE_EAV_ATTRIBUTE_OPTION)],
                ['option_id']
            )
            ->joinInner(
                ['value' => $this->connection->getTableName(self::TABLE_EAV_ATTRIBUTE_OPTION_VALUE)],
                'option.option_id = value.option_id',
                ['value']
            )
            ->where('attribute_id = ?', $attributeId);

        return $this->connection->fetchAll($select);
    }
}
