<?php

namespace Ewave\Navigation\Component;

/**
 * Unify json encoding/decoding algorithm
 * @since 1.3.1
 */
class Json
{
    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data)
    {
        return json_encode($data);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function decode($data)
    {
        return json_decode($data, true);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function decodeAsObject($data)
    {
        return json_decode($data);
    }
}
