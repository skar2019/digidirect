<?php

namespace Ewave\Banner\Component;

class Json
{
    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data)
    {
        return \json_encode($data);
    }

    /**
     * @param string $data
     * @return []
     */
    public function decode($data)
    {
        return \json_decode($data, true);
    }
}
