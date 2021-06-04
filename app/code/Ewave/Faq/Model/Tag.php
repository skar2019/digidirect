<?php
namespace Ewave\Faq\Model;

use Ewave\Faq\Api\Data\FaqInterface;

/**
 * Class Tag
 * @package Ewave\Faq\Model
 */
class Tag extends \Magento\Framework\Model\AbstractModel implements FaqInterface
{
    /**
     * @var CategoryFactory
     */
    protected $_tagFactory;

    /**
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
