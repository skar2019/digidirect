<?php
namespace Digidirect\Faq\Model;

use Digidirect\Faq\Api\Data\FaqInterface;

/**
 * Class Faq
 * @package Digidirect\Faq\Model
 */
class Faq extends \Magento\Framework\Model\AbstractModel implements FaqInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    /**
     * Faq constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Category $resource
     * @param ResourceModel\Category\Collection $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Digidirect\Faq\Model\ResourceModel\Category $resource,
        \Digidirect\Faq\Model\ResourceModel\Category\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }
}
