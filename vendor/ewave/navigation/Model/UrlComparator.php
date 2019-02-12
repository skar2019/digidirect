<?php

namespace Ewave\Navigation\Model;

use Ewave\Navigation\Helper\Data;

class UrlComparator
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var array
     */
    protected $parsedUrls = [];

    /**
     * @var UrlParser
     */
    protected $urlParser;

    /**
     * UrlComparator constructor.
     *
     * @param Data $data
     * @param UrlParser $urlParser
     */
    public function __construct(Data $data, UrlParser $urlParser)
    {
        $this->urlParser = $urlParser;
        $this->helper = $data;
    }

    /**
     * Check if menu item link is active
     *
     * @param string $currentUrl
     * @param string $itemUrl
     * @return bool
     */
    public function isActive($currentUrl, $itemUrl)
    {
        if ($currentUrl == $itemUrl) {
            return true;
        }

        if ($this->helper->compareHandles($itemUrl)) {
            return true;
        }

        $currentUrl = $this->getHostAndPath($currentUrl);
        $itemUrl = $this->getHostAndPath($itemUrl);

        return $currentUrl == $itemUrl;
    }

    /**
     * Return concatenated host and path
     *
     * @param string $url
     * @return string
     */
    protected function getHostAndPath($url)
    {
        if (!isset($this->parsedUrls[$url])) {
            $parsedUrl = $this->getParseUrlResultByUrl($url);
            $this->parsedUrls[$url] = trim(($parsedUrl['host'] ?? '') . ($parsedUrl['path'] ?? ''), '/');
        }
        return $this->parsedUrls[$url];
    }

    /**
     * @param string $url
     * @param null $param
     * @return []
     */
    public function getParseUrlResultByUrl($url, $param = null)
    {
        return $this->urlParser->parseUrl($url, $param);
    }
}
