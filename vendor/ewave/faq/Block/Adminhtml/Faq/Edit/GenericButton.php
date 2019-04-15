<?php
namespace Ewave\Faq\Block\Adminhtml\Faq\Edit;

use Ewave\Faq\Model\Registry\Constants;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class GenericButton
 * @package Ewave\Faq\Block\Adminhtml\Faq\Edit
 */
class GenericButton
{
    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
        $this->request = $context->getRequest();
    }

    /**
     * Return the current faq item Id.
     *
     * @return int|null
     */
    public function getFaqId()
    {
        $faqItem = $this->getCurrentFaqItem();
        return $faqItem ? $faqItem->getId() : null;
    }

    /**
     * Get editable item
     *
     * @return \Ewave\Faq\Model\Faq
     */
    public function getCurrentFaqItem()
    {
        return $this->registry->registry(Constants::CURRENT_FAQ_ITEM);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
