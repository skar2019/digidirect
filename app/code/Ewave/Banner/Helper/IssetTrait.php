<?php

namespace Ewave\Banner\Helper;

trait IssetTrait
{
    /**
     * @param array $array
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getByKey(array $array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}
