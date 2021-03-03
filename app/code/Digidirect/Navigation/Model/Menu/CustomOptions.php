<?php

namespace Digidirect\Navigation\Model\Menu;

/**
 * Class CustomOptions
 * Custom options processor. Used to centralize custom options usage
 */
class CustomOptions
{
    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param []|mixed $customOptions
     * @return string
     */
    public function serialize($customOptions)
    {
        if (!is_array($customOptions)) {
            $customOptions = [];
        }
        return serialize($customOptions);
    }

    /**
     * @param string $customOptions
     * @return array|mixed
     */
    public function unserialize($customOptions)
    {
        if (!is_string($customOptions)) {
            return [];
        }
        try {
            if (!$customOptions) {
                return [];
            }
            if (!isset($this->cache[$customOptions])) {
                $this->cache[$customOptions] = unserialize($customOptions);
            }
        } catch (\Throwable $exception) {
            $this->cache[$customOptions] = [];
        }
        return $this->cache[$customOptions];
    }
}
