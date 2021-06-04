<?php
namespace Ewave\ExtendedCatalogPriceRule\Model\ResourceModel\ExtendedCatalogRule;

use Ewave\ExtendedCatalogPriceRule\Api\Data\RuleDisplayMessageInterface;
use Ewave\ExtendedCatalogPriceRule\Api\ExtendedCatalogRuleManagementInterface;
use Ewave\ExtendedCatalogPriceRule\Model\ExtendedCatalogRule;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\AttributeInterface;

/**
 * Class ReadHandler
 * @package Ewave\ExtendedCatalogPriceRule\Model\ResourceModel\ExtendedCatalogRule
 */
class ReadHandler implements AttributeInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var ExtendedCatalogRuleManagementInterface
     */
    protected $extendedCatalogRuleManagement;

    /**
     * @param MetadataPool $metadataPool
     * @param ExtendedCatalogRuleManagementInterface $extendedCatalogRuleManagement
     */
    public function __construct(
        MetadataPool $metadataPool,
        ExtendedCatalogRuleManagementInterface $extendedCatalogRuleManagement
    ) {
        $this->metadataPool = $metadataPool;
        $this->extendedCatalogRuleManagement = $extendedCatalogRuleManagement;
    }

    /**
     * @param string $entityType
     * @param array $entityData
     * @param array $arguments
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entityType, $entityData, $arguments = [])
    {
        if ($entityData['simple_action'] !== RuleDisplayMessageInterface::ACTION_CODE) {
            return $entityData;
        }

        $idField = $this->metadataPool->getMetadata($entityType)->getIdentifierField();
        $entityId = $entityData[$idField];

        /** @var ExtendedCatalogRule $extendedRule */
        $extendedRule = $this->extendedCatalogRuleManagement->loadByCatalogRuleId($entityId);
        if ($extendedRule->getId()) {
            $extendedData = $extendedRule->getData();
            $entityData = array_merge($extendedData, $entityData);
        }
        return $entityData;
    }
}
