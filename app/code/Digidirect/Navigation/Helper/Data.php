<?php

namespace Digidirect\Navigation\Helper;

use Digidirect\Navigation\Model\Frontend\StoreResolver;
use Digidirect\Navigation\Model\UrlParser;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const DIGIDIRECT_NAVIGATION_CURRENT_SET_REGISTRY_KEY = 'current_navigation_set';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var int
     */
    protected $currentStoreId;

    /**
     * @var UrlParser
     */
    protected $urlParser;

    /**
     * @var StoreResolver
     */
    protected $storeResolver;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param State $appState
     * @param UrlParser $urlParser
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        State $appState,
        UrlParser $urlParser,
        StoreResolver $storeResolver
    ) {
        $this->storeResolver = $storeResolver;
        $this->urlParser = $urlParser;
        $this->storeManager = $storeManager;
        $this->appState = $appState;

        parent::__construct($context);
    }

    /**
     * Get default store id
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Store::DEFAULT_STORE_ID;
    }

    /**
     * Check if currently in admin area
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAdmin()
    {
        return $this->appState->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML;
    }

    /**
     * Get current store id
     *
     * @return int
     */
    public function getCurrentStoreId()
    {
        if (!$this->currentStoreId) {
            $this->currentStoreId = $this->isAdmin()
                ? !$this->_getRequest()->getParam('store')
                    ? $this->getDefaultStoreId()
                    : $this->_getRequest()->getParam('store')
                : $this->storeManager->getStore()->getId();
        }
        return $this->currentStoreId;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Set current store id (used in admin filtering)
     *
     * @param int $storeId
     * @return void
     */
    public function setCurrentStoreId($storeId)
    {
        $this->currentStoreId = $storeId;
    }

    /**
     * Get menu item url
     *
     * @param string $route
     * @param [] $params
     * @return string
     */
    public function getMenuItemUrl($route, $params = [])
    {
        return $this->_getUrl($route, $params);
    }

    /**
     * @param string $itemUrl
     * @return bool
     */
    public function compareHandles($itemUrl)
    {
        $itemUrl = str_replace('/' . $this->storeResolver->getStoreCode(), '', $itemUrl);
        $itemUrl = str_replace('/index.php', '', $itemUrl);
        $itemUrlParsed = $this->urlParser->parseUrl($itemUrl);
        $itemPath = $itemUrlParsed['path'] ?? '';
        $itemPath = trim($itemPath, '/');
        if ($itemPath) {
            $itemPathAsArray = explode('/', $itemPath);
            $partsCount = count($itemPathAsArray);
            if ($partsCount < 3) {
                if ($partsCount == 1) {
                    $itemPathAsArray[1] = 'index';
                }
                $itemPathAsArray[2] = 'index';
            }
            $itemHandle = implode('_', $itemPathAsArray);
            $currentHandle = $this->_getRequest()->getFullActionName();
            return $itemHandle == $currentHandle;
        }
        return false;
    }

    /**
     * Check if link has phone prefix
     *
     * @param string $link
     * @return bool
     */
    public function hasPhonePrefix($link)
    {
        return substr($this->removeSpaces($link), 0, 4) == \Digidirect\Navigation\Model\ResourceModel\Menu::PHONE_PREFIX;
    }

    /**
     * Remove spaces from string
     *
     * @param string $string
     * @return string
     */
    public function removeSpaces($string)
    {
        return preg_replace('/\s+/', '', $string);
    }
}
