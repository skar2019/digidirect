<?php

namespace Digidirect\Navigation\Model\Menu\Type;

use Digidirect\Navigation\Model\UrlComparator;
use Magento\Framework\App\ObjectManager;

/**
 * Custom link class
 */
class CustomLinkAbstract extends AbstractType implements MenuDataInterface
{
    /**
     * @return string
     */
    protected function _getPlaceholder()
    {
        $link = $this->item->getLink();

        if (is_string($link) && preg_match('/({{.*}}).*/', $link, $matches)) {
            return $matches[1];
        }
        return '';
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if (!$this->item->getLink()) {
            return '';
        }
        if ($this->isCustomUrl() || $this->isPhoneNumber()) {
            return $this->item->getLink();
        }
        $link = trim($this->item->getLink());

        $placeholder = $this->_getPlaceholder() ?? $link;
        $isSecure = $this->_isSecureUrl($placeholder);
        $route = $placeholder ? substr($link, strlen($placeholder . '/')) : ltrim($link, '/');

        $params = [
            '_secure' => $isSecure,
            '_direct' => $route,

        ];
        return $route ? $this->helper->getMenuItemUrl($route, $params) : null;
    }

    /**
     * Check if specified url is fully specified url
     *
     * @return bool
     */
    public function isCustomUrl()
    {
        $link = $this->item->getLink();
        if (!$link) {
            return false;
        }
        $lastSymbol = '/';
        $lastSymbolUrl = substr($link, -1);
        if ($lastSymbolUrl != $lastSymbol) {
            $link .= $lastSymbol;
        }
        $url = $this->getUrlComparator()->getParseUrlResultByUrl($link);
        return isset($url['scheme']) && isset($url['host']) && preg_match('/\/$/', $link);
    }

    /**
     * Check if specified URL is phone number
     *
     * @return bool
     */
    public function isPhoneNumber()
    {
        $link = $this->item->getLink();
        return $this->helper->hasPhonePrefix($link);
    }

    /**
     * @return array
     */
    public function getMenuData()
    {
        $isLink = !$this->item->getLink() ? false : true;
        return ['is_link' => $isLink];
    }

    /**
     * @return array
     */
    public function isValidLink()
    {
        $errors = [];

        if ($this->isCustomUrl()) {
            return $errors;
        }
        return $errors;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return parent::isAvailable();
    }

    /**
     * @return UrlComparator
     */
    protected function getUrlComparator()
    {
        return ObjectManager::getInstance()->get(UrlComparator::class);
    }
}
