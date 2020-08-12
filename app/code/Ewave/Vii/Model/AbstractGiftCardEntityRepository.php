<?php

namespace Ewave\Vii\Model;

/**
 * Class AbstractGiftCardEntityRepository
 * @package Ewave\Vii\Model
 */
class AbstractGiftCardEntityRepository implements \Ewave\Vii\Api\AbstractGiftCardEntityRepositoryInterface
{
    /**
     * @var \Ewave\AbstractGiftCard\Model\ResourceModel\AbstractGiftCardEntity
     */
    protected $resource;

    /**
     * AbstractGiftCardEntityRepository constructor.
     * @param ResourceModel\AbstractGiftCardEntity $resource
     */
    public function __construct(
        \Ewave\Vii\Model\ResourceModel\AbstractGiftCardEntity $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * @param \Magento\Framework\DataObject $entityQuoteData
     * @return \Ewave\AbstractGiftCard\Model\ResourceModel\AbstractGiftCardEntity
     */
    public function saveEntityQuoteData(\Magento\Framework\DataObject $entityQuoteData)
    {
        return $this->resource->saveEntityQuoteData($entityQuoteData);
    }

    /**
     * @param \Ewave\AbstractGiftCard\Model\AbstractGiftCardEntity $entity
     * @param int $quoteId
     * @return \Magento\Framework\DataObject|null
     */
    public function getEntityQuoteData($entity, $quoteId)
    {
        return $this->resource->getEntityQuoteData($entity, $quoteId);
    }
}
