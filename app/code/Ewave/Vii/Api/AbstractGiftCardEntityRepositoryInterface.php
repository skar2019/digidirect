<?php

namespace Ewave\Vii\Api;

/**
 * Interface AbstractGiftCardEntityRepositoryInterface
 * @package Ewave\Vii\Api
 */
interface AbstractGiftCardEntityRepositoryInterface
{
    /**
     * @param \Magento\Framework\DataObject $entityQuoteData
     * @return \Ewave\AbstractGiftCard\Model\ResourceModel\AbstractGiftCardEntity
     */
    public function saveEntityQuoteData(\Magento\Framework\DataObject $entityQuoteData);
}
