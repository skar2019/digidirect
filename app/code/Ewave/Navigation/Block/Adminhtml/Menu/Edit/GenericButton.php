<?php
namespace Ewave\Navigation\Block\Adminhtml\Menu\Edit;

use Ewave\Navigation\Model\Registry\Constants;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class GenericButton
 *
 * @package Ewave\Navigation\Block\Adminhtml\Menu\Edit
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
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $auth;

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
        $this->auth = $context->getAuthorization();
    }

    /**
     * Return the current menu item Id.
     *
     * @return int|null
     */
    public function getMenuId()
    {
        $menuItem = $this->getCurrentMenuItem();
        return $menuItem ? $menuItem->getId() : null;
    }

    /**
     * Get editable item
     *
     * @return \Ewave\Navigation\Model\Menu
     */
    public function getCurrentMenuItem()
    {
        return $this->registry->registry(Constants::CURRENT_MENU_ITEM);
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
