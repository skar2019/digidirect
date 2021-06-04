<?php
namespace Ewave\Blog\Block\Post\Comments;

use Ewave\Blog\Api\Data\PostInterface;
use Ewave\Blog\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;

/**
 * Class AbstractCommentType
 */
abstract class AbstractCommentType extends Template
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * GoogleType constructor.
     * @param Template\Context $context
     * @param Data $dataHelper
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $dataHelper,
        Registry $registry,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->registry = $registry;
    }

    /**
     * @return \Ewave\Blog\Model\Post
     */
    public function getPost()
    {
        return $this->registry->registry(PostInterface::CURRENT_ITEM);
    }

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->getPost()->getId();
    }
}
