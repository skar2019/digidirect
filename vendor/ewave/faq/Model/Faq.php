<?php
namespace Ewave\Faq\Model;

use Ewave\Faq\Api\Data\FaqInterface;

/**
 * Class Faq
 * @package Ewave\Faq\Model
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
        \Ewave\Faq\Model\ResourceModel\Category $resource,
        \Ewave\Faq\Model\ResourceModel\Category\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }
}
