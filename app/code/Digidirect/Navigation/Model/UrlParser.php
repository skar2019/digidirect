<?php

namespace Digidirect\Navigation\Model;

/**
 * Class stores processed parse_url function due it is used a lot
 */
class UrlParser
{
    /**
     * @var array
     */
    protected $parsedUrls = [];

    /**
     * @param string $url
     * @param null $param
     * @return mixed
     */
    public function parseUrl($url, $param = null)
    {
        if (is_object($param) || is_array($param)) {
            $param = null;
        }

        $cacheKey = $url . '_' . $param;
        if (!isset($this->parsedUrls[$cacheKey])) {
            if ($param) {
                $this->parsedUrls[$cacheKey] = parse_url($url, $param);
            } else {
                $this->parsedUrls[$cacheKey] = parse_url($url);
            }
        }

        return $this->parsedUrls[$cacheKey];
    }
}
